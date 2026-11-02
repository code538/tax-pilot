<?php

namespace App\Http\Controllers\Api\V1\Subscription;

use App\Http\Controllers\Controller;
use App\Models\OrganisationSubscription;
use App\Models\SubscriptionTransaction;
use App\Services\Payment\PayPalService;
use App\Services\Subscription\SubscriptionPeriodService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class PayPalCaptureController extends Controller
{
    public function __construct(
        protected PayPalService $paypalService,
        protected SubscriptionPeriodService $periodService
    ) {}

    public function capture(Request $request, int $organisationId): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'string', 'max:100'],
        ]);

        $orderId = $validated['order_id'];

        $subscription = OrganisationSubscription::query()
            ->where('organisation_id', $organisationId)
            ->where('provider', 'paypal')
            ->where('metadata->paypal_order_id', $orderId)
            ->first();

        if (! $subscription) {
            return response()->json([
                'success' => false,
                'message' => 'PayPal subscription was not found.',
            ], 404);
        }

        // Idempotency: don't capture or activate an already-active subscription again.
        if ($subscription->status === 'active') {
            return response()->json([
                'success' => true,
                'message' => 'Subscription is already active.',
                'data' => [
                    'subscription_id' => $subscription->id,
                    'status' => $subscription->status,
                    'paypal_capture_id' => data_get(
                        $subscription->metadata,
                        'paypal_capture_id'
                    ),
                ],
            ]);
        }

        if ($subscription->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Subscription is not pending.',
                'status' => $subscription->status,
            ], 422);
        }

        try {
            try {
                $paypalOrder = $this->paypalService->captureOrder($orderId);
            } catch (RuntimeException $exception) {
                // Only use getOrder() for this specific PayPal error.
                if (! str_contains($exception->getMessage(), 'ORDER_ALREADY_CAPTURED')) {
                    throw $exception;
                }

                $paypalOrder = $this->paypalService->getOrder($orderId);
            }

            if (($paypalOrder['status'] ?? null) !== 'COMPLETED') {
                return response()->json([
                    'success' => false,
                    'message' => 'PayPal payment has not completed.',
                    'paypal_status' => $paypalOrder['status'] ?? null,
                ], 422);
            }

            $unit = $paypalOrder['purchase_units'][0] ?? [];
            $captureData = $unit['payments']['captures'][0] ?? [];
            $capturedAmount = $captureData['amount'] ?? [];

            if (
                empty($captureData['id']) ||
                ($capturedAmount['currency_code'] ?? null) !== $subscription->currency ||
                (int) round(((float) ($capturedAmount['value'] ?? 0)) * 100)
                    !== (int) $subscription->amount_minor
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'PayPal capture details did not match the subscription.',
                ], 422);
            }

            DB::transaction(function () use ($subscription, $captureData) {
                $lockedSubscription = OrganisationSubscription::query()
                    ->whereKey($subscription->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // A repeated request must not activate or record the same payment twice.
                if ($lockedSubscription->status === 'active') {
                    return;
                }

                if ($lockedSubscription->status !== 'pending') {
                    throw new RuntimeException('Subscription is no longer pending.');
                }

                $price = $lockedSubscription->price()->firstOrFail();
                $periodStart = now();

                $periodEnd = $this->periodService->periodEnd(
                    $periodStart,
                    $price->billing_interval,
                    (int) $price->interval_count
                );

                $lockedSubscription->update([
                    'status' => 'active',
                    'starts_at' => $periodStart,
                    'current_period_starts_at' => $periodStart,
                    'current_period_ends_at' => $periodEnd,
                    'metadata' => array_merge($lockedSubscription->metadata ?? [], [
                        'paypal_capture_id' => $captureData['id'],
                    ]),
                ]);

                // Create the transaction here using your actual migration fields.
                // Use paypal_capture_id as provider_transaction_id and make this
                // operation idempotent (unique provider_transaction_id).
                SubscriptionTransaction::firstOrCreate(
                    [
                        'provider_transaction_id' => $captureData['id'],
                    ],
                    [
                        'organisation_subscription_id' => $lockedSubscription->id,
                        'provider' => 'paypal',
                        'amount_minor' => (int) $lockedSubscription->amount_minor,
                        'currency' => $lockedSubscription->currency,
                        'status' => 'succeeded',
                        'transaction_type' => 'payment',
                        'paid_at' => now(),
                        'metadata' => [
                            'paypal_order_id' => data_get(
                                $lockedSubscription->metadata,
                                'paypal_order_id'
                            ),
                        ],
                    ]
                );
            });

            return response()->json([
                'success' => true,
                'message' => 'PayPal payment verified and subscription activated.',
                'data' => [
                    'subscription_id' => $subscription->id,
                    'status' => 'active',
                    'paypal_capture_id' => $captureData['id'],
                ],
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Unable to verify PayPal payment and activate subscription.',
            ], 502);
        }
    }
}