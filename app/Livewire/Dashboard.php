<?php

namespace App\Livewire;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Group;
use App\Models\Message;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class Dashboard extends Component
{
    public $selectedGroupId = null;
    public $dateRange = 'all';
    public $readyToLoad = false;

    public function loadStats()
    {
        $this->readyToLoad = true;
    }

    public function render()
    {
        // Caché de estadísticas básicas por 5 minutos
        $cacheKey = "dashboard_stats_{$this->selectedGroupId}_{$this->dateRange}";
        
        if (!$this->readyToLoad) {
            return view('livewire.dashboard', [
                'totalContacts' => 0,
                'validContacts' => 0,
                'totalGroups' => 0,
                'totalCampaigns' => 0,
                'totalSent' => 0,
                'totalDelivered' => 0,
                'totalRead' => 0,
                'totalFailed' => 0,
                'deliveryRate' => 0,
                'readRate' => 0,
                'groupStats' => collect(),
                'recentCampaigns' => collect(),
                'groups' => collect(),
            ]);
        }

        $totalContacts = Cache::remember('dashboard_total_contacts', 300, fn() => Contact::count());
        $validContacts = Cache::remember('dashboard_valid_contacts', 300, fn() => Contact::where('is_valid', true)->count());
        $totalGroups = Cache::remember('dashboard_total_groups', 300, fn() => Group::count());
        $totalCampaigns = Cache::remember('dashboard_total_campaigns', 300, fn() => Campaign::count());

        // Usar agregación directa en BD en lugar de cargar todos los registros
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

        // Usar agregación SQL directa en lugar de cargar todo en memoria
        $stats = Cache::remember($cacheKey . '_stats', 300, function() use ($campaignsQuery) {
            return $campaignsQuery->selectRaw('
                COALESCE(SUM(sent_count), 0) as total_sent,
                COALESCE(SUM(delivered_count), 0) as total_delivered,
                COALESCE(SUM(read_count), 0) as total_read,
                COALESCE(SUM(failed_count), 0) as total_failed
            ')->first();
        });

        $totalSent = $stats->total_sent ?? 0;
        $totalDelivered = $stats->total_delivered ?? 0;
        $totalRead = $stats->total_read ?? 0;
        $totalFailed = $stats->total_failed ?? 0;

        $deliveryRate = $totalSent > 0 ? min(100, round(($totalDelivered / $totalSent) * 100, 2)) : 0;
        $readRate = $totalDelivered > 0 ? min(100, round(($totalRead / $totalDelivered) * 100, 2)) : 0;

        // Simplificar groupStats - solo cargar cuando sea necesario
        $groupStats = Cache::remember($cacheKey . '_group_stats', 300, function() {
            return DB::table('groups')
                ->leftJoin('contacts', 'groups.id', '=', 'contacts.group_id')
                ->leftJoin('campaigns', 'groups.id', '=', 'campaigns.group_id')
                ->select(
                    'groups.id',
                    'groups.name',
                    DB::raw('COUNT(DISTINCT CASE WHEN contacts.is_valid = true THEN contacts.id END) as contacts_count'),
                    DB::raw('COUNT(DISTINCT campaigns.id) as campaigns_count'),
                    DB::raw('COALESCE(SUM(campaigns.sent_count), 0) as total_sent'),
                    DB::raw('COALESCE(SUM(campaigns.delivered_count), 0) as total_delivered'),
                    DB::raw('COALESCE(SUM(campaigns.read_count), 0) as total_read')
                )
                ->groupBy('groups.id', 'groups.name')
                ->get()
                ->map(function ($group) {
                    $deliveryRate = $group->total_sent > 0 
                        ? min(100, round(($group->total_delivered / $group->total_sent) * 100, 2)) 
                        : 0;
                    $readRate = $group->total_delivered > 0 
                        ? min(100, round(($group->total_read / $group->total_delivered) * 100, 2)) 
                        : 0;

                    return [
                        'id' => $group->id,
                        'name' => $group->name,
                        'contacts_count' => $group->contacts_count,
                        'campaigns_count' => $group->campaigns_count,
                        'total_sent' => $group->total_sent,
                        'delivery_rate' => $deliveryRate,
                        'read_rate' => $readRate,
                    ];
                });
        });

        // Solo cargar ID y datos mínimos sin relaciones pesadas
        $recentCampaigns = Cache::remember($cacheKey . '_recent', 180, function() {
            return Campaign::select('id', 'name', 'status', 'group_id', 'sent_count', 'delivered_count', 'read_count', 'created_at')
                ->with('group:id,name')
                ->latest()
                ->take(10)
                ->get();
        });

        $groups = Cache::remember('dashboard_groups', 300, fn() => Group::select('id', 'name')->orderBy('name')->get());

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
