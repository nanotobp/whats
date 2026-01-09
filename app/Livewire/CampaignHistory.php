<?php

namespace App\Livewire;

use App\Models\Campaign;
use App\Services\SupabaseStorage;
use Livewire\Component;
use Livewire\WithPagination;

class CampaignHistory extends Component
{
    use WithPagination;

    public function deleteCampaign($campaignId)
    {
        $campaign = Campaign::findOrFail($campaignId);

        if ($campaign->image_path) {
            $supabase = new SupabaseStorage();
            $supabase->delete($campaign->image_path);
        }

        $campaign->delete();

        session()->flash('message', 'Campaña eliminada del historial.');
        $this->resetPage();
    }

    public function render()
    {
        $campaigns = Campaign::with('group')
            ->whereIn('status', ['completed', 'failed'])
            ->latest('updated_at')
            ->paginate(20);

        return view('livewire.campaign-history', [
            'campaigns' => $campaigns,
        ]);
    }
}
