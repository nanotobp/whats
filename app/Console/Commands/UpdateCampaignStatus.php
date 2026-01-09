<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use Illuminate\Console\Command;

class UpdateCampaignStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaign:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update campaign status to completed if all messages are processed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $campaigns = Campaign::where('status', 'sending')->get();

        if ($campaigns->isEmpty()) {
            $this->info('No campaigns in sending status.');
            return 0;
        }

        $updated = 0;

        foreach ($campaigns as $campaign) {
            $totalProcessed = $campaign->sent_count + $campaign->failed_count;

            if ($totalProcessed >= $campaign->total_recipients) {
                $campaign->update(['status' => 'completed']);
                $this->info("Campaign #{$campaign->id} '{$campaign->name}' updated to completed.");
                $updated++;
            } else {
                $this->warn("Campaign #{$campaign->id} '{$campaign->name}' is still processing ({$totalProcessed}/{$campaign->total_recipients}).");
            }
        }

        $this->info("Updated {$updated} campaigns.");
        return 0;
    }
}
