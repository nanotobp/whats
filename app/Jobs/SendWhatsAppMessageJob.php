<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\Campaign;
use App\Services\GreenApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\Middleware\RateLimited;

class SendWhatsAppMessageJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $timeout = 60;
    public $backoff = [30, 60, 120];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Message $message,
        public Campaign $campaign
    ) {
        $this->onQueue('whatsapp');
    }

    /**
     * Execute the job.
     */
    public function handle(GreenApiService $greenApi): void
    {
        try {
            $contact = $this->message->contact;

            if (!$contact || !$contact->is_valid) {
                $this->message->update([
                    'status' => 'failed',
                    'error_message' => 'Contact not valid or not found'
                ]);
                return;
            }

            $hasImage = !empty($this->campaign->image_path);

            if ($hasImage) {
                $imageUrl = url('storage/' . $this->campaign->image_path);
                $result = $greenApi->sendFileByUrl(
                    $contact->phone,
                    $imageUrl,
                    $this->campaign->content
                );
            } else {
                $result = $greenApi->sendMessage(
                    $contact->phone,
                    $this->campaign->content
                );
            }

            if ($result['success']) {
                $this->message->update([
                    'status' => 'sent',
                    'whatsapp_message_id' => $result['message_id'] ?? null,
                    'sent_at' => now()
                ]);

                $this->campaign->increment('sent_count');
            } else {
                $this->message->update([
                    'status' => 'failed',
                    'error_message' => $result['error'] ?? 'Unknown error'
                ]);

                $this->campaign->increment('failed_count');
            }

            // Check if campaign is completed using fresh data from database
            $campaign = Campaign::find($this->campaign->id);
            $totalProcessed = $campaign->sent_count + $campaign->failed_count;

            if ($totalProcessed >= $campaign->total_recipients && $campaign->status === 'sending') {
                $campaign->update(['status' => 'completed']);
            }

            sleep(2);

        } catch (\Exception $e) {
            Log::error('Send WhatsApp Message Job Failed', [
                'message_id' => $this->message->id,
                'campaign_id' => $this->campaign->id,
                'error' => $e->getMessage()
            ]);

            $this->message->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            $this->campaign->increment('failed_count');

            throw $e;
        }
    }

    public function middleware(): array
    {
        return [new RateLimited('whatsapp')];
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Send WhatsApp Message Job Permanently Failed', [
            'message_id' => $this->message->id,
            'campaign_id' => $this->campaign->id,
            'error' => $exception->getMessage()
        ]);
    }
}
