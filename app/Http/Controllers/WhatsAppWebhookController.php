<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    private const GREETINGS = ['hi', 'hii', 'hiii', 'hello', 'helo', 'hey', 'hlo', 'namaste'];

    private const WELCOME_MESSAGE = "Hi! 👋 Welcome to UnlistedGain.\n\nWe help you buy and sell unlisted / pre-IPO shares. Reply with:\n1️⃣ Buy shares\n2️⃣ Sell shares\n3️⃣ Talk to our team\n\nOr just tell us what you're looking for.";

    /**
     * GET — Meta's one-time handshake when the Callback URL is saved.
     * Must echo back hub_challenge as plain text if the verify token matches.
     */
    public function verify(Request $request)
    {
        if (
            $request->query('hub_mode') === 'subscribe'
            && $request->query('hub_verify_token') === config('services.whatsapp.verify_token')
        ) {
            return response($request->query('hub_challenge'), 200);
        }

        return response('Forbidden', 403);
    }

    /**
     * POST — incoming messages and status updates. Meta expects a fast 200;
     * it retries on anything else, so we never throw here.
     */
    public function receive(Request $request): Response
    {
        $payload = $request->all();
        Log::info('WhatsApp webhook payload', $payload);

        try {
            $messages = data_get($payload, 'entry.0.changes.0.value.messages', []);

            foreach ($messages as $message) {
                if (($message['type'] ?? null) !== 'text') {
                    continue;
                }

                $from = $message['from'] ?? null;
                $body = trim($message['text']['body'] ?? '');

                if ($from && in_array(mb_strtolower($body), self::GREETINGS, true)) {
                    app(WhatsAppService::class)->sendTextMessage($from, self::WELCOME_MESSAGE);
                }
            }
        } catch (\Throwable $e) {
            Log::error('WhatsApp webhook processing failed: ' . $e->getMessage());
        }

        return response('EVENT_RECEIVED', 200);
    }
}
