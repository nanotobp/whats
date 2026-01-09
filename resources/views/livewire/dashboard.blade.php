<div>
    <h2 class="text-3xl font-bold mb-6">Dashboard</h2>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-1">Total Contactos</div>
            <div class="text-3xl font-bold text-gray-800">{{ number_format($totalContacts) }}</div>
            <div class="text-sm text-green-600 mt-1">{{ number_format($validContacts) }} válidos</div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-1">Campañas</div>
            <div class="text-3xl font-bold text-gray-800">{{ number_format($totalCampaigns) }}</div>
            <div class="text-sm text-gray-600 mt-1">{{ number_format($totalGroups) }} grupos</div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-1">Mensajes Enviados</div>
            <div class="text-3xl font-bold text-gray-800">{{ number_format($totalSent) }}</div>
            <div class="text-sm text-green-600 mt-1">{{ $deliveryRate }}% entregados</div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-1">Tasa de Lectura</div>
            <div class="text-3xl font-bold text-gray-800">{{ $readRate }}%</div>
            <div class="text-sm text-gray-600 mt-1">{{ number_format($totalRead) }} leídos</div>
        </div>
    </div>

    <!-- Recent Campaigns -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-xl font-semibold">Campañas Recientes</h3>
        </div>
        <div class="p-6">
            @if($recentCampaigns->count() > 0)
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Nombre</th>
                            <th class="text-left py-2">Estado</th>
                            <th class="text-right py-2">Enviados</th>
                            <th class="text-left py-2">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentCampaigns as $campaign)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3">{{ $campaign->name }}</td>
                                <td>
                                    <span class="px-2 py-1 rounded text-xs bg-green-100 text-green-800">
                                        {{ ucfirst($campaign->status) }}
                                    </span>
                                </td>
                                <td class="text-right">{{ number_format($campaign->sent_count) }}</td>
                                <td>{{ $campaign->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-center py-4 text-gray-500">No hay campañas aún. ¡Crea tu primera campaña!</p>
            @endif
        </div>
    </div>
</div>
