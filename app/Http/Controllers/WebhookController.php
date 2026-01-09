<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function greenApi(Request $request)
    {
        try {
            $data = $request->all();

            Log::info('Green API Webhook Received', $data);

            // Check if it's a message status update
            if (isset($data['typeWebhook']) && $data['typeWebhook'] === 'outgoingMessageStatus') {
                $this->handleMessageStatus($data);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Webhook Error', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function handleMessageStatus($data)
    {
        $messageId = $data['idMessage'] ?? null;
        $status = $data['status'] ?? null;
        $timestamp = $data['timestamp'] ?? null;

        if (!$messageId || !$status) {
            return;
        }

        // Find message by WhatsApp message ID
        $message = Message::where('whatsapp_message_id', $messageId)->first();

        if (!$message) {
            Log::warning('Message not found for webhook', ['message_id' => $messageId]);
            return;
        }

        $campaign = $message->campaign;

        // Convert timestamp to Carbon datetime if provided (Green API sends Unix timestamps in UTC)
        $timestampDate = $timestamp ? \Carbon\Carbon::createFromTimestamp($timestamp, 'UTC')->setTimezone(config('app.timezone')) : now();

        // Update message status based on webhook
        switch ($status) {
            case 'sent':
                if (!$message->sent_at) {
                    $message->update([
                        'status' => 'sent',
                        'sent_at' => $timestampDate
                    ]);
                }
                break;

            case 'delivered':
                // Only update if message hasn't been delivered or read yet
                if (!in_array($message->status, ['delivered', 'read'])) {
                    $message->update([
                        'status' => 'delivered',
                        'delivered_at' => $timestampDate
                    ]);

                    if ($campaign) {
                        $campaign->increment('delivered_count');
                    }
                }
                break;

            case 'read':
                $wasRead = $message->status === 'read';
                $wasDelivered = in_array($message->status, ['delivered', 'read']);

                $message->update([
                    'status' => 'read',
                    'read_at' => $timestampDate
                ]);

                if ($campaign && !$wasRead) {
                    // If message wasn't delivered before, count it as delivered now
                    if (!$wasDelivered) {
                        $campaign->increment('delivered_count');
                    }
                    $campaign->increment('read_count');
                }
                break;

            case 'failed':
                $message->update([
                    'status' => 'failed',
                    'error_message' => $data['description'] ?? 'Message failed'
                ]);

                if ($campaign) {
                    $campaign->increment('failed_count');
                }
                break;
        }

        Log::info('Message status updated', [
            'message_id' => $message->id,
            'whatsapp_id' => $messageId,
            'status' => $status,
            'timestamp' => $timestampDate
        ]);
    }
}
