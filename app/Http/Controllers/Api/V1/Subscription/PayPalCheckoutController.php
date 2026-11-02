<?php

namespace App\Http\Controllers\Api\V1\Subscription;

use App\Http\Controllers\Controller;
use App\Models\OrganisationSubscription;
use App\Models\SubscriptionPlanPrice;
use App\Services\Payment\PayPalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class PayPalCheckoutController extends Controller
{
    public function __construct(
        protected PayPalService $paypalService
    ) {}

    public function create(
        Request $request,
        int $organisationId
    ): JsonResponse {
        $validated = $request->validate([
            'subscription_plan_price_id' => [
                'required',
                'integer',
                'exists:subscription_plan_prices,id',
            ],
        ]);
        //dd(now());
        // dd([
        //     'now' => now()->toDateTimeString(),
        //     'timezone' => config('app.timezone'),
        //     'price' => SubscriptionPlanPrice::find($validated['subscription_plan_price_id'])?->toArray(),
        // ]);
        //dd($validated['subscription_plan_price_id']);
        $price = SubscriptionPlanPrice::query()
            ->whereKey($validated['subscription_plan_price_id'])
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('effective_from')
                    ->orWhere('effective_from', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('effective_until')
                    ->orWhere('effective_until', '>', now());
            })
            ->with(['plan.product'])
            ->first();
        //dd($price);
        if (! $price || ! $price->plan || ! $price->plan->is_active
            || ! $price->plan->product
            || ! $price->plan->product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'The selected subscription price is unavailable.',
            ], 422);
        }
        //dd($price->amount_minor);
        $amount = number_format($price->amount_minor / 100, 2, '.', '');
        $currency = strtoupper($price->currency);
        //dd($amount, $currency, $organisationId, $price->id);
        try {
            $order = $this->paypalService->createOrder(
                $amount,
                $currency,
                $price->plan->name . ' subscription',
                'organisation:' . $organisationId . '|price:' . $price->id
            );
            //dd($order);
            $subscription = DB::transaction(function () use (
                $organisationId,
                $price,
                $order,
                $amount,
                $currency
            ) {
                return OrganisationSubscription::create([
                    'organisation_id' => $organisationId,
                    'subscription_plan_id' => $price->subscription_plan_id,
                    'subscription_plan_price_id' => $price->id,
                    'status' => 'pending',
                    'provider' => 'paypal',
                    'amount_minor' => $price->amount_minor,
                    'currency' => $currency,
                    'billing_interval' => $price->billing_interval,
                    'metadata' => [
                        'paypal_order_id' => $order['id'],
                        'checkout_amount' => $amount,
                    ],
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'PayPal checkout order created.',
                'data' => [
                    'subscription_id' => $subscription->id,
                    'subscription_status' => $subscription->status,
                    'paypal_order_id' => $order['id'],
                    'approval_url' => $order['approval_url'],
                    'amount' => $amount,
                    'currency' => $currency,
                ],
            ], 201);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Unable to start PayPal checkout. Please try again.',
            ], 502);
        }
    }
}