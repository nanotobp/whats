<?php

namespace App\Livewire;

use App\Models\Group;
use Livewire\Component;

class GroupManager extends Component
{
    public $name;
    public $description;
    public $editingId = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
    ];

    public function createGroup()
    {
        $this->validate();

        Group::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        $this->reset(['name', 'description']);
        session()->flash('message', 'Grupo creado exitosamente.');
    }

    public function editGroup($id)
    {
        $group = Group::findOrFail($id);
        $this->editingId = $id;
        $this->name = $group->name;
        $this->description = $group->description;
    }

    public function updateGroup()
    {
        $this->validate();

        $group = Group::findOrFail($this->editingId);
        $group->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        $this->reset(['name', 'description', 'editingId']);
        session()->flash('message', 'Grupo actualizado exitosamente.');
    }

    public function deleteGroup($id)
    {
        Group::findOrFail($id)->delete();
        session()->flash('message', 'Grupo eliminado.');
    }

    public function cancelEdit()
    {
        $this->reset(['name', 'description', 'editingId']);
    }

    public function render()
    {
        $groups = Group::withCount('contacts')->latest()->get();

        return view('livewire.group-manager', [
            'groups' => $groups,
        ]);
    }
}
