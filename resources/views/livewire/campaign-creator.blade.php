<div>
    <h2 class="text-3xl font-bold mb-6">Crear Campaña</h2>

    <!-- Create Campaign Form with Two Columns -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-xl font-semibold mb-4">Nueva Campaña</h3>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column: Compose Form -->
            <div>
                <h4 class="text-lg font-medium mb-4 text-gray-700">Componer Mensaje</h4>
                <form wire:submit.prevent="createCampaign">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Campaña</label>
                        <input type="text" wire:model="name" class="border rounded px-3 py-2 w-full" placeholder="Ej: Comunicado Importante - Enero 2026">
                        @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contenido del Mensaje</label>
                        <textarea wire:model.live="content" rows="7" class="border rounded px-3 py-2 w-full" placeholder="Escribe tu mensaje aquí... Puedes incluir links."></textarea>
                        @error('content') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        <p class="text-sm text-gray-500 mt-1">Máximo 4096 caracteres</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Imagen (opcional)</label>
                        <input type="file" wire:model="image" accept="image/*" class="border rounded px-3 py-2 w-full">
                        @error('image') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        <p class="text-sm text-gray-500 mt-1">JPG, PNG, máximo 5MB</p>
                    </div>

                    <div class="mb-4">
                        <label class="flex items-center mb-2">
                            <input type="checkbox" wire:model.live="sendToAll" class="mr-2">
                            <span class="text-sm font-medium">Enviar a todos los contactos válidos</span>
                        </label>

                        @if(!$sendToAll)
                            <div class="ml-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Grupo</label>
                                <select wire:model="groupId" class="border rounded px-3 py-2 w-full">
                                    <option value="">Selecciona un grupo</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    <button type="submit"
                            class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove>Crear Campaña</span>
                        <span wire:loading>Creando...</span>
                    </button>
                </form>
            </div>

            <!-- Right Column: iPhone Mockup -->
            <div class="flex justify-center items-start">
                <div class="relative">
                    <!-- iPhone Frame -->
                    <div class="relative w-[340px] h-[680px] bg-black rounded-[50px] shadow-2xl p-3">
                        <!-- iPhone Notch -->
                        <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-40 h-7 bg-black rounded-b-3xl z-10"></div>

                        <!-- iPhone Screen -->
                        <div class="w-full h-full bg-gray-100 rounded-[40px] overflow-hidden">
                            <!-- WhatsApp Header -->
                            <div class="bg-green-600 px-4 py-3 flex items-center">
                                <div class="w-10 h-10 bg-gray-300 rounded-full mr-3"></div>
                                <div>
                                    <div class="text-white font-semibold text-sm">Tu Empresa</div>
                                    <div class="text-green-100 text-xs">en línea</div>
                                </div>
                            </div>

                            <!-- Chat Area -->
                            <div class="flex-1 p-4 overflow-y-auto" style="height: 580px; background-color: #e5ddd5;">
                                @php
                                    $hasContent = !empty($content);
                                    $hasImage = $image !== null;

                                    // Detect URLs in content
                                    $urls = [];
                                    if ($hasContent) {
                                        preg_match_all('#https?://[^\s]+#', $content, $matches);
                                        $urls = $matches[0] ?? [];
                                    }
                                @endphp

                                @if($hasContent || $hasImage)
                                    <!-- Message Bubble -->
                                    <div class="flex justify-start mb-2">
                                        <div class="bg-white rounded-lg shadow-sm" style="max-width: 85%; padding: 12px;">
                                            @if($hasImage)
                                                <div style="margin-bottom: 8px;">
                                                    <div class="w-full bg-gray-200 rounded flex items-center justify-center" style="height: 192px;">
                                                        <svg class="text-gray-400" style="width: 48px; height: 48px;" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">Vista previa de imagen</div>
                                                </div>
                                            @endif

                                            @if($hasContent)
                                                <div class="text-gray-800 whitespace-pre-wrap break-words" style="font-size: 14px;">{{ $content }}</div>

                                                @if(count($urls) > 0)
                                                    @php
                                                        $firstUrl = $urls[0];
                                                        $parsedUrl = parse_url($firstUrl);
                                                        $host = $parsedUrl['host'] ?? 'Link Preview';
                                                    @endphp
                                                    <!-- Link Preview -->
                                                    <div class="border border-gray-200 rounded overflow-hidden" style="margin-top: 12px;">
                                                        <div class="bg-gray-100 flex items-center justify-center" style="height: 128px;">
                                                            <svg class="text-gray-400" style="width: 40px; height: 40px;" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"/>
                                                            </svg>
                                                        </div>
                                                        <div class="bg-white" style="padding: 8px;">
                                                            <div class="text-gray-800 font-semibold truncate" style="font-size: 12px;">{{ $host }}</div>
                                                            <div class="text-gray-500 truncate" style="font-size: 11px;">{{ $firstUrl }}</div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif

                                            <div class="text-gray-500 text-right" style="font-size: 11px; margin-top: 4px;">{{ now()->format('H:i') }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center justify-center" style="height: 100%;">
                                        <div class="text-center text-gray-500">
                                            <svg class="mx-auto mb-2 text-gray-300" style="width: 64px; height: 64px;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                                            </svg>
                                            <p style="font-size: 14px;">La vista previa aparecerá aquí</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaigns List -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b flex justify-between items-center">
            <h3 class="text-xl font-semibold">Campañas</h3>
            <button wire:click="loadCampaigns" class="bg-gray-600 text-white px-3 py-1 rounded text-sm hover:bg-gray-700">
                <span wire:loading.remove wire:target="loadCampaigns">Actualizar</span>
                <span wire:loading wire:target="loadCampaigns">Actualizando...</span>
            </button>
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
                                        @if($campaign->failed_count > 0)
                                            <span class="text-red-600">
                                                Fallidos: <strong>{{ number_format($campaign->failed_count) }}</strong>
                                            </span>
                                        @endif

                                        @php
                                            $statusLabels = [
                                                'draft' => 'Borrador',
                                                'sending' => 'Enviando',
                                                'completed' => 'Completado',
                                                'failed' => 'Fallido'
                                            ];
                                            $totalProcessed = $campaign->sent_count + $campaign->failed_count;
                                        @endphp

                                        <span class="px-2 py-1 rounded text-xs font-semibold
                                            {{ $campaign->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $campaign->status === 'sending' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $campaign->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $campaign->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ $statusLabels[$campaign->status] ?? ucfirst($campaign->status) }}
                                        </span>

                                        @if($campaign->status === 'sending')
                                            <span class="text-blue-600 font-medium">
                                                ({{ number_format($totalProcessed) }} de {{ number_format($campaign->total_recipients) }} usuarios)
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    @if($campaign->status === 'draft')
                                        <button wire:click="sendCampaign({{ $campaign->id }})"
                                                class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">
                                            Enviar
                                        </button>
                                    @endif
                                    @if($campaign->status !== 'draft')
                                        <a href="{{ route('campaign.metrics', $campaign->id) }}"
                                           class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 inline-block">
                                            Ver Métricas
                                        </a>
                                    @endif
                                    <button wire:click="deleteCampaign({{ $campaign->id }})"
                                            onclick="return confirm('¿Eliminar esta campaña?')"
                                            class="bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700">
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center py-8 text-gray-500">No hay campañas. ¡Crea tu primera campaña arriba!</p>
            @endif
        </div>
    </div>
</div>
