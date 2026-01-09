<?php

namespace App\Livewire;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Group;
use App\Models\Message;
use App\Jobs\SendWhatsAppMessageJob;
use App\Services\SupabaseStorage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class CampaignCreator extends Component
{
    use WithFileUploads;

    public $name;
    public $content;
    public $image;
    public $sendToAll = true;
    public $groupId;
    public $campaigns;

    protected $rules = [
        'name' => 'required|string|max:255',
        'content' => 'required|string|max:4096',
        'image' => 'nullable|image|max:5120',
    ];

    public function mount()
    {
        $this->loadCampaigns();
    }

    public function loadCampaigns()
    {
        $this->campaigns = Campaign::with('group')
            ->whereIn('status', ['draft', 'sending'])
            ->latest()
            ->take(10)
            ->get();
    }

    public function createCampaign()
    {
        $this->validate();

        try {
            $imagePath = null;
            $imageUrl = null;

            if ($this->image) {
                $supabase = new SupabaseStorage();
                $result = $supabase->upload($this->image, 'campaigns');

                if ($result['success']) {
                    $imagePath = $result['path'];
                    $imageUrl = $result['url'];
                } else {
                    session()->flash('error', 'Error al subir imagen: ' . $result['error']);
                    return;
                }
            }

            $campaign = Campaign::create([
                'name' => $this->name,
                'content' => $this->content,
                'image_path' => $imagePath,
                'image_url' => $imageUrl,
                'group_id' => $this->sendToAll ? null : $this->groupId,
                'send_to_all' => $this->sendToAll,
                'status' => 'draft',
            ]);

            $this->reset(['name', 'content', 'image', 'sendToAll', 'groupId']);
            $this->loadCampaigns();

            session()->flash('message', 'Campaña creada exitosamente.');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear campaña: ' . $e->getMessage());
        }
    }

    public function sendCampaign($campaignId)
    {
        $campaign = Campaign::findOrFail($campaignId);

        if ($campaign->status !== 'draft') {
            session()->flash('error', 'Esta campaña ya fue enviada.');
            return;
        }

        $query = Contact::where('is_valid', true);

        if (!$campaign->send_to_all && $campaign->group_id) {
            $query->where('group_id', $campaign->group_id);
        }

        $contacts = $query->get();

        if ($contacts->isEmpty()) {
            session()->flash('error', 'No hay contactos válidos para enviar.');
            return;
        }

        $campaign->update([
            'status' => 'sending',
            'total_recipients' => $contacts->count(),
            'started_at' => now(),
        ]);

        foreach ($contacts as $index => $contact) {
            $message = Message::create([
                'campaign_id' => $campaign->id,
                'contact_id' => $contact->id,
                'status' => 'pending',
            ]);

            SendWhatsAppMessageJob::dispatch($message, $campaign)
                ->delay(now()->addSeconds($index * 3));
        }

        $this->loadCampaigns();
        session()->flash('message', "Campaña iniciada. Se enviarán {$contacts->count()} mensajes.");
    }

    public function deleteCampaign($campaignId)
    {
        $campaign = Campaign::findOrFail($campaignId);

        if ($campaign->image_path) {
            $supabase = new SupabaseStorage();
            $supabase->delete($campaign->image_path);
        }

        $campaign->delete();

        $this->loadCampaigns();
        session()->flash('message', 'Campaña eliminada.');
    }

    public function render()
    {
        $groups = Group::orderBy('name')->get();

        return view('livewire.campaign-creator', [
            'groups' => $groups,
        ]);
    }
}
