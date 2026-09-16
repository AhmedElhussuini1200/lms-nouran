<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * إشعارات واتساب لأولياء الأمور عبر WhatsApp Cloud API.
 * لو الخدمة غير مفعلة (مفيش توكن) → يتخطى بهدوء ويسجل في الـ Log.
 */
class WhatsappService
{
    public function provider(): string
    {
        return config('services.whatsapp.provider', 'meta');
    }

    public function enabled(): bool
    {
        if (!(bool) config('services.whatsapp.enabled')) {
            return false;
        }

        // المجاني (CallMeBot) والذاتي (Gateway/Evolution) لا يحتاجان توكن Meta
        if (in_array($this->provider(), ['callmebot', 'gateway', 'evolution'])) {
            return true;
        }

        return !empty(config('services.whatsapp.token'))
            && !empty(config('services.whatsapp.phone_id'));
    }

    public function send(string $to, string $message, ?string $apiKey = null, ?int $adminId = null): bool
    {
        $to = $this->normalize($to);

        if (!$this->enabled() || !$to) {
            $this->log($adminId, $to ?? '', $message, 'skipped', 'service disabled or invalid number');
            Log::info('[WhatsApp skipped]', ['to' => $to, 'message' => $message]);
            return false;
        }

        try {
            $ok = match ($this->provider()) {
                'callmebot' => $this->sendViaCallMeBot($to, $message, $apiKey),
                'gateway' => $this->sendViaGateway($to, $message),
                'evolution' => $this->sendViaEvolution($to, $message),
                default => $this->sendViaMeta($to, $message),
            };
            $this->log($adminId, $to, $message, $ok ? 'sent' : 'failed', $ok ? null : 'provider returned failure');
            return $ok;
        } catch (\Throwable $e) {
            $this->log($adminId, $to ?? '', $message, 'failed', $e->getMessage());
            Log::warning('[WhatsApp exception] ' . $e->getMessage());
            return false;
        }
    }

    protected function log(?int $adminId, string $phone, string $message, string $status, ?string $error = null): void
    {
        try {
            \App\Models\WhatsappLog::create([
                'admin_id' => $adminId,
                'phone' => $phone,
                'message' => mb_substr($message, 0, 1000),
                'provider' => $this->provider(),
                'status' => $status,
                'error' => $error ? mb_substr($error, 0, 500) : null,
                'sent_by' => auth('admin')->id(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('[WhatsApp log failed] ' . $e->getMessage());
        }
    }

    /**
     * بوابة النظام الخاصة (Baileys — رقمك الشخصي).
     * الرسالة تخرج من رقمك وتظهر في واتسابك كرسالة مرسلة.
     */
    protected function sendViaGateway(string $to, string $message): bool
    {
        $base = rtrim(config('services.whatsapp.gateway_url', 'http://127.0.0.1:3100'), '/');
        $key = config('services.whatsapp.gateway_key');

        if (!$base || !$key) {
            Log::warning('[WhatsApp gateway] missing config');
            return false;
        }

        $response = Http::withHeaders(['x-api-key' => $key])->post("{$base}/send", [
            'to' => $to,
            'message' => $message,
        ]);

        if (!$response->successful() || !($response->json('ok') ?? false)) {
            Log::warning('[WhatsApp gateway failed]', ['to' => $to, 'response' => $response->body()]);
            return false;
        }

        return true;
    }

    public function gatewayStatus(): array
    {
        try {
            $base = rtrim(config('services.whatsapp.gateway_url', 'http://127.0.0.1:3100'), '/');
            $response = Http::timeout(5)->get("{$base}/status");
            return $response->successful() ? $response->json() : ['ok' => false];
        } catch (\Throwable $e) {
            return ['ok' => false];
        }
    }

    /**
     * بوابة ذاتية مجانية (Evolution API — Baileys).
     * التشغيل: docker run ... ثم مسح QR مرة واحدة من لوحة التحكم.
     * بعدها الإرسال من الداشبورد لأي رقم بدون تسجيل مسبق.
     */
    protected function sendViaEvolution(string $to, string $message): bool
    {
        $base = rtrim(config('services.whatsapp.evolution_url'), '/');
        $instance = config('services.whatsapp.evolution_instance');
        $key = config('services.whatsapp.evolution_key');

        if (!$base || !$instance || !$key) {
            Log::warning('[WhatsApp evolution] missing config');
            return false;
        }

        // الرقم بالصيغة الدولية بدون + (مصر: 201xxxxxxxxx)
        $response = Http::withHeaders(['apikey' => $key])->post(
            "{$base}/message/sendText/{$instance}",
            ['number' => $to, 'text' => $message]
        );

        if (!$response->successful()) {
            Log::warning('[WhatsApp evolution failed]', ['to' => $to, 'response' => $response->body()]);
            return false;
        }

        return true;
    }

    /**
     * المزود المجاني: https://www.callmebot.com
     * كل رقم لازم يفعل مرة واحدة: يبعت "I allow callmebot to send me messages"
     * لرقم CallMeBot وياخد apikey خاص بيه.
     */
    protected function sendViaCallMeBot(string $to, string $message, ?string $apiKey = null): bool
    {
        $apiKey = $apiKey ?: config('services.whatsapp.callmebot_apikey');

        if (!$apiKey) {
            Log::warning('[WhatsApp callmebot] missing apikey', ['to' => $to]);
            return false;
        }

        $response = Http::get('https://api.callmebot.com/whatsapp.php', [
            'phone' => $to,
            'text' => $message,
            'apikey' => $apiKey,
        ]);

        if (!$response->successful() || str_contains($response->body(), 'ERROR')) {
            Log::warning('[WhatsApp callmebot failed]', ['to' => $to, 'response' => $response->body()]);
            return false;
        }

        return true;
    }

    protected function sendViaMeta(string $to, string $message): bool
    {
        $response = Http::withToken(config('services.whatsapp.token'))
            ->post('https://graph.facebook.com/v21.0/' . config('services.whatsapp.phone_id') . '/messages', [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'text',
                'text' => ['body' => $message],
            ]);

        if (!$response->successful()) {
            Log::warning('[WhatsApp failed]', ['to' => $to, 'response' => $response->body()]);
            return false;
        }

        return true;
    }

    /**
     * تطبيع رقم مصري: 01xxxxxxxxx → 201xxxxxxxxx
     */
    public function normalize(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', convertArabicNumbers($phone, 'none') ?? $phone);

        if (str_starts_with($digits, '0020')) {
            $digits = substr($digits, 2);
        }
        if (str_starts_with($digits, '20') && strlen($digits) === 12) {
            return $digits;
        }
        if (str_starts_with($digits, '01') && strlen($digits) === 11) {
            return '2' . $digits;
        }

        return strlen($digits) >= 10 ? $digits : null;
    }
}
