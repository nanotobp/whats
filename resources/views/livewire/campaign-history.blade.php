<div>
    <h2 class="text-3xl font-bold mb-6">Historial Enviado</h2>

    <!-- Campaigns History List -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-xl font-semibold">Campañas Completadas</h3>
        </div>
        <div class="p-6">
            @if($campaigns && $campaigns->count() > 0)
                <div class="space-y-4">
                    @foreach($campaigns as $campaign)
                        <div class="border rounded p-4 hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-lg">{{ $campaign->name }}</h4>
                                    <p class="text-gray-600 text-sm mt-1">{{ Str::limit($campaign->content, 100) }}</p>
                                    <div class="mt-2 flex gap-4 text-sm flex-wrap items-center">
                                        <span class="text-gray-600">
                                            Destinatarios: <strong>{{ number_format($campaign->total_recipients) }}</strong>
                                        </span>
                                        <span class="text-green-600">
                                            Enviados: <strong>{{ number_format($campaign->sent_count) }}</strong>
                                        </span>
                                        <span class="text-blue-600">
                                            Leídos: <strong>{{ number_format($campaign->read_count) }}</strong>
                                        </span>
                                        @if($campaign->failed_count > 0)
                                            <span class="text-red-600">
                                                Fallidos: <strong>{{ number_format($campaign->failed_count) }}</strong>
                                            </span>
                                        @endif

                                        @php
                                            $statusLabels = [
                                                'completed' => 'Completado',
                                                'failed' => 'Fallido'
                                            ];
                                        @endphp

                                        <span class="px-2 py-1 rounded text-xs font-semibold
                                            {{ $campaign->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $campaign->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ $statusLabels[$campaign->status] ?? ucfirst($campaign->status) }}
                                        </span>

                                        @if($campaign->completed_at)
                                            <span class="text-gray-500 text-xs">
                                                Completado: {{ $campaign->completed_at->format('d/m/Y H:i') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('campaign.metrics', $campaign->id) }}"
                                       class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 inline-block">
                                        Ver Métricas
                                    </a>
                                    <button wire:click="deleteCampaign({{ $campaign->id }})"
                                            onclick="return confirm('¿Eliminar esta campaña del historial?')"
                                            class="bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700">
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $campaigns->links() }}
                </div>
            @else
                <p class="text-center py-8 text-gray-500">No hay campañas completadas en el historial.</p>
            @endif
        </div>
    </div>
</div>
