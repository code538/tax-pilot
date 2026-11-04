<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use RuntimeException;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    /**
     * Get PayPal API base URL.
     */
    protected function baseUrl(): string
    {
        return rtrim(
            config('services.paypal.base_url', 'https://api-m.sandbox.paypal.com'),
            '/'
        );
    }

    /**
     * Generate PayPal OAuth access token.
     */
    public function getAccessToken(): string
    {
        $clientId = config('services.paypal.client_id');
        $clientSecret = config('services.paypal.client_secret');

        if (empty($clientId) || empty($clientSecret)) {
            throw new RuntimeException(
                'PayPal Client ID or Client Secret is missing.'
            );
        }

        $response = Http::asForm()
            ->withBasicAuth($clientId, $clientSecret)
            ->acceptJson()
            ->timeout(30)
            ->post($this->baseUrl() . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        if ($response->failed()) {
            throw new RuntimeException(
                'Unable to obtain PayPal access token. HTTP status: '
                . $response->status()
            );
        }

        $accessToken = $response->json('access_token');

        if (empty($accessToken)) {
            throw new RuntimeException(
                'PayPal response did not contain an access token.'
            );
        }

        return $accessToken;
    }

    /**
     * Create a PayPal checkout order.
     */
    public function createOrder(
        string $amount,
        string $currency,
        string $description,
        string $customId
    ): array {
        $token = $this->getAccessToken();
        //dd($token);
        $response = \Illuminate\Support\Facades\Http::withToken($token)
            ->acceptJson()
            ->asJson()
            ->timeout(30)
            ->withHeaders([
                'PayPal-Request-Id' => (string) \Illuminate\Support\Str::uuid(),
            ])
            ->post($this->baseUrl() . '/v2/checkout/orders', [
                'intent' => 'CAPTURE',

                'purchase_units' => [
                    [
                        'custom_id' => $customId,
                        'description' => $description,

                        'amount' => [
                            'currency_code' => strtoupper($currency),
                            'value' => $amount,
                        ],
                    ],
                ],

                'payment_source' => [
                    'paypal' => [
                        'experience_context' => [
                            'return_url' => config('services.paypal.return_url'),
                            'cancel_url' => config('services.paypal.cancel_url'),
                            'user_action' => 'PAY_NOW',
                        ],
                    ],
                ],
            ]);
        //dd($response->json());
        if ($response->failed()) {
            throw new \RuntimeException(
                'PayPal order creation failed. HTTP status: ' .
                $response->status()
            );
        }

        // if ($response->failed()) {
        //     Log::error('PayPal order creation failed', [
        //         'http_status' => $response->status(),
        //         'response' => $response->json(),
        //     ]);

        //     throw new RuntimeException(
        //         'PayPal order creation failed. HTTP status: ' .
        //         $response->status()
        //     );
        // }

        $data = $response->json();

        $links = collect($data['links'] ?? []);

        $approvalUrl = $links->firstWhere('rel', 'approve')['href']
            ?? $links->firstWhere('rel', 'payer-action')['href']
            ?? null;

        if (empty($data['id']) || empty($approvalUrl)) {
            throw new \RuntimeException(
                'PayPal response did not contain an order ID or approval URL.'
            );
        }

        return [
            'id' => $data['id'],
            'status' => $data['status'] ?? null,
            'approval_url' => $approvalUrl,
        ];
    }

    public function captureOrder(string $orderId): array
    {
        $token = $this->getAccessToken();
        //dd($token);
        // $response = Http::withToken($token)
        //     ->acceptJson()
        //     ->asJson()
        //     ->timeout(30)
        //     ->withHeaders([
        //         'PayPal-Request-Id' => (string) \Illuminate\Support\Str::uuid(),
        //     ])
        //     ->post($this->baseUrl() . '/v2/checkout/orders/' . urlencode($orderId) . '/capture', []);
        //dd($this->baseUrl() . '/v2/checkout/orders/' . urlencode($orderId) . '/capture');
        $response = Http::withToken($token)
            ->acceptJson()
            ->asJson()
            ->timeout(30)
            ->withHeaders([
                'PayPal-Request-Id' => (string) \Illuminate\Support\Str::uuid(),
            ])
            ->post(
                $this->baseUrl() . '/v2/checkout/orders/' . urlencode($orderId) . '/capture',
                new \stdClass()
            );
        //dd($response->json());
        if ($response->failed()) {
            \Illuminate\Support\Facades\Log::error('PayPal capture failed', [
                'http_status' => $response->status(),
                'response' => $response->json(),
            ]);

            throw new \RuntimeException(
                'PayPal capture failed. HTTP status: ' . $response->status()
            );
        }

        return $response->json();
    }

    public function getOrder(string $orderId): array
    {
        $response = Http::withToken($this->getAccessToken())
            ->acceptJson()
            ->timeout(30)
            ->get($this->baseUrl() . '/v2/checkout/orders/' . urlencode($orderId));

        if ($response->failed()) {
            throw new \RuntimeException('Unable to fetch PayPal order.');
        }

        return $response->json();
    }

    // create subscription for a plan using PayPal subscription API
    public function createSubscription(
        string $paypalPlanId,
        string $customId
    ): array {
        $response = Http::withToken($this->getAccessToken())
            ->acceptJson()
            ->asJson()
            ->timeout(30)
            ->withHeaders([
                'PayPal-Request-Id' => (string) \Illuminate\Support\Str::uuid(),
            ])
            ->post($this->baseUrl() . '/v1/billing/subscriptions', [
                'plan_id' => $paypalPlanId,
                'custom_id' => $customId,
                'application_context' => [
                    'brand_name' => config('app.name'),
                    'return_url' => config('services.paypal.return_url'),
                    'cancel_url' => config('services.paypal.cancel_url'),
                    'user_action' => 'SUBSCRIBE_NOW',
                ],
            ]);

        if ($response->failed()) {
            \Illuminate\Support\Facades\Log::error(
                'PayPal subscription creation failed',
                [
                    'http_status' => $response->status(),
                    'response' => $response->json(),
                ]
            );

            throw new \RuntimeException(
                'PayPal subscription creation failed. HTTP status: '
                . $response->status()
            );
        }

        $data = $response->json();

        $approvalUrl = collect($data['links'] ?? [])
            ->firstWhere('rel', 'approve')['href'] ?? null;

        if (empty($data['id']) || empty($approvalUrl)) {
            throw new \RuntimeException(
                'PayPal response did not contain a subscription ID or approval URL.'
            );
        }

        return [
            'id' => $data['id'],
            'status' => $data['status'] ?? null,
            'approval_url' => $approvalUrl,
        ];
    }

    public function verifyWebhookSignature(array $headers, array $event): bool
    {
        $response = Http::withToken($this->getAccessToken())
            ->post($this->baseUrl . '/v1/notifications/verify-webhook-signature', [
                'auth_algo'         => $headers['auth_algo'] ?? null,
                'cert_url'          => $headers['cert_url'] ?? null,
                ' transmission_id'  => $headers['transmission_id'] ?? null,
                'transmission_sig'  => $headers['transmission_sig'] ?? null,
                'transmission_time' => $headers['transmission_time'] ?? null,
                'webhook_id'        => config('services.paypal.webhook_id'),
                'webhook_event'     => $event,
            ]);

        $response->throw();

        return $response->json('verification_status') === 'SUCCESS';
    }
}