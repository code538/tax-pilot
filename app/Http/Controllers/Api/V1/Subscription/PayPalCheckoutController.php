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

    // public function create(
    //     Request $request,
    //     int $organisationId
    // ): JsonResponse {
    //     $validated = $request->validate([
    //         'subscription_plan_price_id' => [
    //             'required',
    //             'integer',
    //             'exists:subscription_plan_prices,id',
    //         ],
    //     ]);
       
    //     $price = SubscriptionPlanPrice::query()
    //         ->whereKey($validated['subscription_plan_price_id'])
    //         ->where('is_active', true)
    //         ->where(function ($query) {
    //             $query->whereNull('effective_from')
    //                 ->orWhere('effective_from', '<=', now());
    //         })
    //         ->where(function ($query) {
    //             $query->whereNull('effective_until')
    //                 ->orWhere('effective_until', '>', now());
    //         })
    //         ->with(['plan.product'])
    //         ->first();
    //     //dd($price->plan->product);
    //     if (! $price || ! $price->plan || ! $price->plan->is_active
    //         || ! $price->plan->product
    //         || ! $price->plan->product->is_active) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'The selected subscription price is unavailable.',
    //         ], 422);
    //     }
        
    //     $amount = number_format($price->amount_minor / 100, 2, '.', '');
    //     $currency = strtoupper($price->currency);
        
    //     try {
    //         if ($price->payment_mode === 'one_time') {
    //             $order = $this->paypalService->createOrder(
    //                 $amount,
    //                 $currency,
    //                 $price->plan->name . ' subscription',
    //                 'organisation:' . $organisationId . '|price:' . $price->id
    //             );
    //             // Save pending subscription and return approval URL...
    //         } elseif ($price->payment_mode === 'recurring') {
    //             // Separate PayPal Subscriptions API flow
    //             // Create PayPal subscription using the configured PayPal billing plan.
    //         } else {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Unsupported payment mode.',
    //             ], 422);
    //         }    
    //         //dd($order);
    //         $subscription = DB::transaction(function () use (
    //             $organisationId,
    //             $price,
    //             $order,
    //             $amount,
    //             $currency
    //         ) {
    //             return OrganisationSubscription::create([
    //                 'organisation_id' => $organisationId,
    //                 'subscription_plan_id' => $price->subscription_plan_id,
    //                 'subscription_plan_price_id' => $price->id,
    //                 'status' => 'pending',
    //                 'provider' => 'paypal',
    //                 'amount_minor' => $price->amount_minor,
    //                 'currency' => $currency,
    //                 'billing_interval' => $price->billing_interval,
    //                 'metadata' => [
    //                     'paypal_order_id' => $order['id'],
    //                     'checkout_amount' => $amount,
    //                 ],
    //             ]);
    //         });

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'PayPal checkout order created.',
    //             'data' => [
    //                 'subscription_id' => $subscription->id,
    //                 'subscription_status' => $subscription->status,
    //                 'paypal_order_id' => $order['id'],
    //                 'approval_url' => $order['approval_url'],
    //                 'amount' => $amount,
    //                 'currency' => $currency,
    //             ],
    //         ], 201);
    //     } catch (Throwable $exception) {
    //         report($exception);

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Unable to start PayPal checkout. Please try again.',
    //         ], 502);
    //     }
    // }

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
        //dd($price->plan->product);
        if (! $price || ! $price->plan || ! $price->plan->is_active
            || ! $price->plan->product
            || ! $price->plan->product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'The selected subscription price is unavailable.',
            ], 422);
        }
        
        $amount = number_format($price->amount_minor / 100, 2, '.', '');
        $currency = strtoupper($price->currency);
        
        try {
            if ($price->payment_mode === 'one_time') {
                $order = $this->paypalService->createOrder(
                    $amount,
                    $currency,
                    $price->plan->name . ' subscription',
                    'organisation:' . $organisationId . '|price:' . $price->id
                );

                $subscription = DB::transaction(function () use (
                    $organisationId,
                    $price,
                    $order,
                    $currency,
                    $amount
                ) {
                    return OrganisationSubscription::create([
                        'organisation_id' => $organisationId,
                        'subscription_plan_id' => $price->subscription_plan_id,
                        'subscription_plan_price_id' => $price->id,
                        'status' => 'pending',
                        'provider' => 'paypal',
                        'amount_minor' => $price->amount_minor,
                        'currency' => $currency,
                        'billing_interval' => null,
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
            }

            if ($price->payment_mode === 'recurring') {
                if (empty($price->paypal_plan_id)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'PayPal billing plan is not configured for this price.',
                    ], 422);
                }

                $paypalSubscription = $this->paypalService->createSubscription(
                    $price->paypal_plan_id,
                    'organisation:' . $organisationId . '|price:' . $price->id
                );

                $subscription = DB::transaction(function () use (
                    $organisationId,
                    $price,
                    $paypalSubscription,
                    $currency
                ) {
                    return OrganisationSubscription::create([
                        'organisation_id' => $organisationId,
                        'subscription_plan_id' => $price->subscription_plan_id,
                        'subscription_plan_price_id' => $price->id,
                        'status' => 'pending',
                        'provider' => 'paypal',
                        'provider_subscription_id' => $paypalSubscription['id'],
                        'amount_minor' => $price->amount_minor,
                        'currency' => $currency,
                        'billing_interval' => $price->billing_interval,
                        'metadata' => [
                            'paypal_subscription_id' => $paypalSubscription['id'],
                        ],
                    ]);
                });

                return response()->json([
                    'success' => true,
                    'message' => 'PayPal recurring checkout created.',
                    'data' => [
                        'subscription_id' => $subscription->id,
                        'subscription_status' => $subscription->status,
                        'paypal_subscription_id' => $paypalSubscription['id'],
                        'approval_url' => $paypalSubscription['approval_url'],
                        'amount' => $amount,
                        'currency' => $currency,
                        'billing_interval' => $price->billing_interval,
                    ],
                ], 201);
            }

            return response()->json([
                'success' => false,
                'message' => 'Unsupported payment mode.',
            ], 422);

        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Unable to start PayPal checkout. Please try again.',
            ], 502);
        }
    }


    public function productID(): JsonResponse
    {
        $product = $this->paypalService->createProduct();

        dd($product);
    }



   
  
}