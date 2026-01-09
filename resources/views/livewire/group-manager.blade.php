<div>
    <h2 class="text-3xl font-bold mb-6">Gestión de Grupos</h2>

    <!-- Create/Edit Group Form -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-xl font-semibold mb-4">{{ $editingId ? 'Editar Grupo' : 'Crear Nuevo Grupo' }}</h3>

        <form wire:submit.prevent="{{ $editingId ? 'updateGroup' : 'createGroup' }}">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Grupo</label>
                <input type="text" wire:model="name" class="border rounded px-3 py-2 w-full" placeholder="Ej: Gerencia, Operarios, Administración">
                @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción (opcional)</label>
                <textarea wire:model="description" rows="3" class="border rounded px-3 py-2 w-full" placeholder="Descripción del grupo..."></textarea>
                @error('description') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                    {{ $editingId ? 'Actualizar' : 'Crear' }} Grupo
                </button>

                @if($editingId)
                    <button type="button"
                            wire:click="cancelEdit"
                            class="bg-gray-400 text-white px-6 py-2 rounded hover:bg-gray-500">
                        Cancelar
                    </button>
                @endif
            </div>
        </form>
    </div>

    <!-- Groups List -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-xl font-semibold">Grupos Existentes</h3>
        </div>
        <div class="p-6">
            @if($groups->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($groups as $group)
                        <div class="border rounded p-4 hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-lg">{{ $group->name }}</h4>
                                    @if($group->description)
                                        <p class="text-gray-600 text-sm mt-1">{{ $group->description }}</p>
                                    @endif
                                    <p class="text-gray-500 text-sm mt-2">
                                        <strong>{{ number_format($group->contacts_count) }}</strong> contactos
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <button wire:click="editGroup({{ $group->id }})"
                                            class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                        Editar
                                    </button>
                                    <button wire:click="deleteGroup({{ $group->id }})"
                                            onclick="return confirm('¿Eliminar este grupo?')"
                                            class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700">
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center py-8 text-gray-500">No hay grupos. ¡Crea tu primer grupo arriba!</p>
            @endif
        </div>
    </div>
</div>
