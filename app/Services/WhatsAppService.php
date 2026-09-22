<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $phoneNumberId;
    private string $accessToken;
    private string $apiVersion;

    public function __construct()
    {
        $this->phoneNumberId = (string) config('services.whatsapp.phone_number_id');
        $this->accessToken   = (string) config('services.whatsapp.access_token');
        $this->apiVersion    = (string) config('services.whatsapp.api_version');
    }

    /**
     * Send a free-form text reply. Only deliverable inside the 24h window
     * opened by the customer's last incoming message — use sendTemplateMessage
     * (e.g. for OTPs) to reach someone outside that window.
     */
    public function sendTextMessage(string $to, string $text): bool
    {
        return $this->post([
            'messaging_product' => 'whatsapp',
            'to'                => $this->normalizeNumber($to),
            'type'              => 'text',
            'text'              => ['body' => $text],
        ]);
    }

    /**
     * Send an approved message template, e.g. an OTP code outside the 24h
     * customer-service window. $bodyParams fills the template's {{1}}, {{2}}...
     */
    public function sendTemplateMessage(string $to, ?string $templateName = null, ?string $langCode = null, array $bodyParams = []): bool
    {
        $templateName ??= (string) config('services.whatsapp.otp_template');
        $langCode     ??= (string) config('services.whatsapp.otp_template_lang');

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $this->normalizeNumber($to),
            'type'              => 'template',
            'template'          => [
                'name'     => $templateName,
                'language' => ['code' => $langCode],
            ],
        ];

        if (!empty($bodyParams)) {
            $payload['template']['components'] = [[
                'type'       => 'body',
                'parameters' => array_map(fn ($param) => ['type' => 'text', 'text' => (string) $param], $bodyParams),
            ]];
        }

        return $this->post($payload);
    }

    private function post(array $payload): bool
    {
        if (!$this->phoneNumberId || !$this->accessToken) {
            Log::error('WhatsApp: missing phone_number_id or access_token config.');
            return false;
        }

        $response = Http::withToken($this->accessToken)
            ->post("https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages", $payload);

        if ($response->failed()) {
            Log::error('WhatsApp send failed', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);
        }

        return $response->successful();
    }

    private function normalizeNumber(string $number): string
    {
        return preg_replace('/[^0-9]/', '', $number);
    }
}
