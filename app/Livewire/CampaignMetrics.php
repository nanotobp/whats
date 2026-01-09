<?php

namespace App\Livewire;

use App\Models\Campaign;
use App\Models\Message;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class CampaignMetrics extends Component
{
    public Campaign $campaign;
    public $readDetails = [];
    public $unreadContacts = [];

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign->load(['messages.contact', 'group']);
        $this->loadReadDetails();
    }

    public function loadReadDetails()
    {
        // Get contacts who read the message
        $this->readDetails = $this->campaign->messages()
            ->where('status', 'read')
            ->with('contact')
            ->get()
            ->map(function ($message) {
                return [
                    'name' => $message->contact->name ?? $message->contact->phone,
                    'phone' => $message->contact->phone,
                    'read_at' => $message->read_at,
                    'time_to_read' => $message->sent_at && $message->read_at
                        ? round($message->sent_at->diffInMinutes($message->read_at))
                        : null,
                ];
            });

        // Get contacts who haven't read
        $this->unreadContacts = $this->campaign->messages()
            ->whereIn('status', ['sent', 'delivered', 'pending'])
            ->with('contact')
            ->get()
            ->map(function ($message) {
                return [
                    'name' => $message->contact->name ?? $message->contact->phone,
                    'phone' => $message->contact->phone,
                    'status' => $message->status,
                ];
            });
    }

    public function getHourlyEngagementData()
    {
        $hourlyData = $this->campaign->messages()
            ->whereNotNull('read_at')
            ->select(DB::raw('CAST(strftime("%H", read_at) as INTEGER) as hour'), DB::raw('count(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        $hours = range(0, 23);
        $data = array_fill(0, 24, 0);

        foreach ($hourlyData as $item) {
            $data[$item->hour] = $item->count;
        }

        return [
            'hours' => $hours,
            'reads' => $data,
        ];
    }

    public function getStatusDistribution()
    {
        $distribution = $this->campaign->messages()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        return $distribution->map(function ($item) {
            $labels = [
                'pending' => 'Pendiente',
                'sent' => 'Enviado',
                'delivered' => 'Entregado',
                'read' => 'Leído',
                'failed' => 'Fallido',
            ];

            return [
                'name' => $labels[$item->status] ?? $item->status,
                'value' => $item->count,
            ];
        });
    }

    public function getAverageReadTime()
    {
        $avgMinutes = $this->campaign->messages()
            ->whereNotNull('sent_at')
            ->whereNotNull('read_at')
            ->get()
            ->avg(function ($message) {
                return $message->sent_at->diffInMinutes($message->read_at);
            });

        return round($avgMinutes, 2);
    }

    public function getGroupEngagement()
    {
        if (!$this->campaign->group_id) {
            return null;
        }

        $total = $this->campaign->messages()->count();
        $read = $this->campaign->messages()->where('status', 'read')->count();

        return $total > 0 ? round(($read / $total) * 100, 2) : 0;
    }

    public function render()
    {
        return view('livewire.campaign-metrics', [
            'hourlyData' => $this->getHourlyEngagementData(),
            'statusDistribution' => $this->getStatusDistribution(),
            'avgReadTime' => $this->getAverageReadTime(),
            'groupEngagement' => $this->getGroupEngagement(),
        ]);
    }
}
