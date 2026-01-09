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
        // Solo seleccionar las columnas necesarias
        $campaigns = Campaign::select([
                'id', 'name', 'status', 'group_id', 'message', 
                'sent_count', 'delivered_count', 'read_count', 'failed_count',
                'created_at', 'updated_at', 'image_path'
            ])
            ->with('group:id,name')
            ->whereIn('status', ['completed', 'failed'])
            ->latest('updated_at')
            ->paginate(15); // Reducido de 20 a 15 para cargar más rápido

        return view('livewire.campaign-history', [
            'campaigns' => $campaigns,
        ]);
    }
}
