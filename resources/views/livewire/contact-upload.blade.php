<div>
    <h2 class="text-3xl font-bold mb-6">Gestión de Contactos</h2>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Total Contactos</div>
            <div class="text-2xl font-bold">{{ number_format($totalContacts) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Números Válidos</div>
            <div class="text-2xl font-bold text-green-600">{{ number_format($validContacts) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Tasa de Validez</div>
            <div class="text-2xl font-bold">{{ $totalContacts > 0 ? round(($validContacts / $totalContacts) * 100) : 0 }}%</div>
        </div>
    </div>

    <!-- Upload Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-semibold mb-4">Subir Contactos desde CSV</h3>

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if ($uploadStats)
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <p class="font-bold">¡Importación exitosa!</p>
                <p>Importados: {{ $uploadStats['imported'] }}</p>
                <p>Duplicados: {{ $uploadStats['duplicates'] }}</p>
                <p>Total procesados: {{ $uploadStats['total'] }}</p>
            </div>
        @endif

        <form wire:submit.prevent="uploadContacts">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Archivo CSV</label>
                <input type="file" wire:model="csvFile" accept=".csv,.txt" class="border rounded px-3 py-2 w-full">
                @error('csvFile') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                <p class="text-sm text-gray-500 mt-1">Formato: phone,name (o telefono,nombre)</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Grupo (opcional)</label>
                <select wire:model="groupId" class="border rounded px-3 py-2 w-full">
                    <option value="">Sin grupo</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" wire:model="validateNumbers" class="mr-2">
                    <span class="text-sm">Validar números en WhatsApp (demora más tiempo)</span>
                </label>
            </div>

            <button type="submit"
                    class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700"
                    wire:loading.attr="disabled">
                <span wire:loading.remove>Subir Contactos</span>
                <span wire:loading>Procesando...</span>
            </button>
        </form>
    </div>

    <!-- Contacts List -->
    <div class="mt-6 bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-xl font-semibold">Lista de Contactos</h3>
        </div>
        <div class="p-6">
            @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('message') }}
                </div>
            @endif

            @if($editingId)
                <!-- Edit Form -->
                <div class="bg-gray-50 border border-gray-200 rounded p-4 mb-4">
                    <h4 class="font-semibold mb-3">Editar Contacto</h4>
                    <form wire:submit.prevent="updateContact">
                        <div class="grid grid-cols-2 gap-4 mb-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                <input type="text" wire:model="editPhone" class="border rounded px-3 py-2 w-full">
                                @error('editPhone') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                                <input type="text" wire:model="editName" class="border rounded px-3 py-2 w-full">
                                @error('editName') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Grupo</label>
                                <select wire:model="editGroupId" class="border rounded px-3 py-2 w-full">
                                    <option value="">Sin grupo</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="flex items-center mt-7">
                                    <input type="checkbox" wire:model="editIsValid" class="mr-2">
                                    <span class="text-sm">¿Número válido?</span>
                                </label>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">
                                Guardar Cambios
                            </button>
                            <button type="button" wire:click="cancelEdit" class="bg-gray-400 text-white px-4 py-2 rounded text-sm hover:bg-gray-500">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            @if($contacts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr class="border-b">
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Teléfono</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Nombre</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Grupo</th>
                                <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700">Válido</th>
                                <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contacts as $contact)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4 text-sm">{{ $contact->phone }}</td>
                                    <td class="py-3 px-4 text-sm">{{ $contact->name ?? '-' }}</td>
                                    <td class="py-3 px-4 text-sm">{{ $contact->group?->name ?? '-' }}</td>
                                    <td class="py-3 px-4 text-center">
                                        @if($contact->is_valid)
                                            <span class="inline-block w-3 h-3 bg-green-500 rounded-full"></span>
                                        @else
                                            <span class="inline-block w-3 h-3 bg-gray-300 rounded-full"></span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <button wire:click="editContact({{ $contact->id }})"
                                                class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700 mr-1">
                                            Editar
                                        </button>
                                        <button wire:click="deleteContact({{ $contact->id }})"
                                                onclick="return confirm('¿Eliminar este contacto?')"
                                                class="bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $contacts->links() }}
                </div>
            @else
                <p class="text-center py-8 text-gray-500">No hay contactos. ¡Sube tu primer archivo CSV arriba!</p>
            @endif
        </div>
    </div>

    <div class="mt-6 bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-semibold mb-4">Ejemplo de CSV</h3>
        <pre class="bg-gray-100 p-4 rounded text-sm">phone,name
5491112345678,Juan Pérez
5491187654321,María González
5493512345678,Pedro Rodríguez</pre>
    </div>
</div>
