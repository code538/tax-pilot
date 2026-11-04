<?php

namespace App\Http\Controllers\Api\V1\Subscription;

use App\Http\Controllers\Controller;
use App\Services\Subscription\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayPalWebhookController extends Controller
{
    public function handle(Request $request, PayPalService $paypal)
    {
        $event = $request->all();

        $headers = [
            'auth_algo'         => $request->header('PAYPAL-AUTH-ALGO'),
            'cert_url'          => $request->header('PAYPAL-CERT-URL'),
            'transmission_id'   => $request->header('PAYPAL-TRANSMISSION-ID'),
            'transmission_sig'  => $request->header('PAYPAL-TRANSMISSION-SIG'),
            'transmission_time' => $request->header('PAYPAL-TRANSMISSION-TIME'),
        ];

        try {
            if (! $paypal->verifyWebhookSignature($headers, $event)) {
                return response()->json(['message' => 'Invalid webhook signature.'], 400);
            }

            Log::info('Verified PayPal webhook received', [
                'event_type' => $event['event_type'] ?? null,
                'event_id' => $event['id'] ?? null,
            ]);

            return response()->json(['received' => true]);
        } catch (\Throwable $e) {
            Log::error('PayPal webhook verification failed', [
                'message' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Webhook verification failed.'], 500);
        }
    }
}