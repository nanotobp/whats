<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearDashboardCache extends Command
{
    protected $signature = 'dashboard:clear-cache';
    protected $description = 'Limpiar caché del dashboard';

    public function handle()
    {
        $patterns = [
            'dashboard_*',
            'campaign_metrics_*',
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }

        // Limpiar todo el caché de tags si está disponible
        try {
            Cache::tags(['dashboard', 'campaigns'])->flush();
        } catch (\Exception $e) {
            // Database cache no soporta tags
        }

        $this->info('Caché del dashboard limpiado exitosamente.');
        
        return 0;
    }
}
