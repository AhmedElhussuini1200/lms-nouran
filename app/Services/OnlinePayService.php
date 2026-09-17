<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;

/**
 * بوابات الدفع المصرية: Paymob + Fawry
 * تعمل حقيقياً عند ضبط المفاتيح، وإلا تُرجع وضع mock.
 */
class OnlinePayService
{
    public function paymobIntention(Payment $payment): ?string
    {
        $key = config('services.onlinepay.paymob_key');
        if (! $key) {
            return null; // mock
        }

        try {
            // Paymob Intention API (v1)
            $res = Http::withToken($key)->timeout(20)->post('https://accept.paymob.com/v1/intention/', [
                'amount' => (int) ((float) $payment->remaining * 100),
                'currency' => 'EGP',
                'merchant_order_id' => 'PAY-' . $payment->id . '-' . time(),
                'billing_data' => [
                    'first_name' => $payment->student->name ?? 'student',
                    'email' => $payment->student->email ?? 'student@lms.local',
                    'phone_number' => $payment->student->phone ?? '+201000000000',
                ],
            ]);
            $clientSecret = $res->json('client_secret');

            return $clientSecret ? 'https://accept.paymob.com/unifiedcheckout/?publicKey=' . config('services.onlinepay.paymob_public') . '&clientSecret=' . $clientSecret : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public function fawryCharge(Payment $payment): ?array
    {
        $key = config('services.onlinepay.fawry_key');
        if (! $key) {
            return null; // mock
        }

        try {
            $merchant = config('services.onlinepay.fawry_merchant');
            $ref = 'PAY-' . $payment->id . '-' . time();
            $signature = hash('sha256', $merchant . $ref . number_format((float) $payment->remaining, 2, '.', '') . $key);
            $res = Http::timeout(20)->post('https://www.atfawry.com/ECommerceWebService/rest/payments/charge', [
                'merchantCode' => $merchant,
                'merchantRefNum' => $ref,
                'paymentAmount' => (float) $payment->remaining,
                'signature' => $signature,
                'customer' => ['name' => $payment->student->name ?? '', 'mobile' => $payment->student->phone ?? ''],
            ]);

            return $res->successful() ? $res->json() : null;
        } catch (\Throwable) {
            return null;
        }
    }

    // التحقق من callback (HMAC حسب البوابة)
    public function verifyCallback(string $provider, array $payload): bool
    {
        if ($provider === 'paymob') {
            $hmac = $payload['hmac'] ?? '';
            $secret = config('services.onlinepay.paymob_hmac') ?? '';
            if (! $secret || ! $hmac) {
                return false;
            }
            $keys = ['amount_cents', 'created_at', 'currency', 'error_occured', 'has_parent_transaction', 'id', 'integration_id', 'is_3d_secure', 'is_auth', 'is_capture', 'is_refunded', 'is_standalone_payment', 'is_voided', 'order', 'owner', 'pending', 'source_data_pan', 'source_data_sub_type', 'source_data_type', 'success'];
            $concatenated = implode('', array_map(fn ($k) => $payload['obj'][$k] ?? $payload[$k] ?? '', $keys));
            return hash_equals(hash_hmac('sha512', $concatenated, $secret), strtolower($hmac));
        }

        if ($provider === 'fawry') {
            $key = config('services.onlinepay.fawry_key') ?? '';
            $sig = $payload['signature'] ?? $payload['messageSignature'] ?? '';
            if (! $key || ! $sig) {
                return false;
            }
            $expected = hash('sha256', ($payload['merchantRefNumber'] ?? '') . ($payload['orderAmount'] ?? '') . $key);

            return hash_equals($expected, $sig);
        }

        return false;
    }
}
