<?php

namespace App\Livewire;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Group;
use App\Models\Message;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $selectedGroupId = null;
    public $dateRange = 'all';

    public function render()
    {
        $totalContacts = Contact::count();
        $validContacts = Contact::where('is_valid', true)->count();
        $totalGroups = Group::count();
        $totalCampaigns = Campaign::count();

        $campaignsQuery = Campaign::query();

        if ($this->selectedGroupId) {
            $campaignsQuery->where('group_id', $this->selectedGroupId);
        }

        if ($this->dateRange !== 'all') {
            $date = match($this->dateRange) {
                'today' => now()->startOfDay(),
                'week' => now()->subWeek(),
                'month' => now()->subMonth(),
                default => null,
            };

            if ($date) {
                $campaignsQuery->where('created_at', '>=', $date);
            }
        }

        $campaigns = $campaignsQuery->with('group')->latest()->get();

        $totalSent = $campaigns->sum('sent_count');
        $totalDelivered = $campaigns->sum('delivered_count');
        $totalRead = $campaigns->sum('read_count');
        $totalFailed = $campaigns->sum('failed_count');

        $deliveryRate = $totalSent > 0 ? min(100, round(($totalDelivered / $totalSent) * 100, 2)) : 0;
        $readRate = $totalDelivered > 0 ? min(100, round(($totalRead / $totalDelivered) * 100, 2)) : 0;

        $groupStats = Group::withCount([
            'contacts' => function ($query) {
                $query->where('is_valid', true);
            }
        ])
        ->with(['campaigns' => function ($query) {
            $query->latest()->take(5);
        }])
        ->get()
        ->map(function ($group) {
            $totalSent = $group->campaigns->sum('sent_count');
            $totalDelivered = $group->campaigns->sum('delivered_count');
            $totalRead = $group->campaigns->sum('read_count');

            return [
                'id' => $group->id,
                'name' => $group->name,
                'contacts_count' => $group->contacts_count,
                'campaigns_count' => $group->campaigns->count(),
                'total_sent' => $totalSent,
                'delivery_rate' => $totalSent > 0 ? min(100, round(($totalDelivered / $totalSent) * 100, 2)) : 0,
                'read_rate' => $totalDelivered > 0 ? min(100, round(($totalRead / $totalDelivered) * 100, 2)) : 0,
            ];
        });

        $recentCampaigns = Campaign::with(['group', 'messages'])
            ->latest()
            ->take(10)
            ->get();

        $groups = Group::orderBy('name')->get();

        return view('livewire.dashboard', [
            'totalContacts' => $totalContacts,
            'validContacts' => $validContacts,
            'totalGroups' => $totalGroups,
            'totalCampaigns' => $totalCampaigns,
            'totalSent' => $totalSent,
            'totalDelivered' => $totalDelivered,
            'totalRead' => $totalRead,
            'totalFailed' => $totalFailed,
            'deliveryRate' => $deliveryRate,
            'readRate' => $readRate,
            'groupStats' => $groupStats,
            'recentCampaigns' => $recentCampaigns,
            'groups' => $groups,
        ]);
    }
}
