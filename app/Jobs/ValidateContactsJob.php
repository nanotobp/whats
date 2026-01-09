<?php

namespace App\Jobs;

use App\Models\Contact;
use App\Services\GreenApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ValidateContactsJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $contactIds
    ) {
        $this->onQueue('validation');
    }

    /**
     * Execute the job.
     */
    public function handle(GreenApiService $greenApi): void
    {
        $contacts = Contact::whereIn('id', $this->contactIds)->get();

        foreach ($contacts as $contact) {
            try {
                $result = $greenApi->checkWhatsapp($contact->phone);

                $contact->update([
                    'is_valid' => $result['exists']
                ]);

                usleep(500000);

            } catch (\Exception $e) {
                Log::error('Validate Contact Failed', [
                    'contact_id' => $contact->id,
                    'phone' => $contact->phone,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
