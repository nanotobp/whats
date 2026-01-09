<?php

namespace App\Livewire;

use App\Models\Contact;
use App\Models\Group;
use App\Jobs\ValidateContactsJob;
use League\Csv\Reader;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;

class ContactUpload extends Component
{
    use WithFileUploads;

    public $csvFile;
    public $groupId;
    public $validateNumbers = true;
    public $uploading = false;
    public $uploadStats = null;

    // Edit contact properties
    public $editingId = null;
    public $editPhone = '';
    public $editName = '';
    public $editGroupId = null;
    public $editIsValid = false;

    protected $rules = [
        'csvFile' => 'required|file|mimes:csv,txt|max:10240',
    ];

    protected function editRules()
    {
        return [
            'editPhone' => 'required|string',
            'editName' => 'nullable|string',
            'editGroupId' => 'nullable|exists:groups,id',
            'editIsValid' => 'boolean',
        ];
    }

    public function uploadContacts()
    {
        $this->validate();

        $this->uploading = true;

        try {
            $path = $this->csvFile->getRealPath();
            $csv = Reader::createFromPath($path, 'r');
            $csv->setHeaderOffset(0);

            $records = $csv->getRecords();
            $imported = 0;
            $duplicates = 0;
            $contactIds = [];

            DB::beginTransaction();

            foreach ($records as $record) {
                $phone = $this->cleanPhoneNumber($record['phone'] ?? $record['telefono'] ?? $record['numero'] ?? '');

                if (empty($phone)) {
                    continue;
                }

                $existingContact = Contact::where('phone', $phone)->first();

                if ($existingContact) {
                    $duplicates++;
                    continue;
                }

                $contact = Contact::create([
                    'phone' => $phone,
                    'name' => $record['name'] ?? $record['nombre'] ?? null,
                    'group_id' => $this->groupId,
                    'is_valid' => false,
                ]);

                $contactIds[] = $contact->id;
                $imported++;
            }

            DB::commit();

            if ($this->validateNumbers && count($contactIds) > 0) {
                $chunks = array_chunk($contactIds, 50);
                foreach ($chunks as $chunk) {
                    ValidateContactsJob::dispatch($chunk);
                }
            }

            $this->uploadStats = [
                'imported' => $imported,
                'duplicates' => $duplicates,
                'total' => $imported + $duplicates,
            ];

            if ($this->groupId) {
                $group = Group::find($this->groupId);
                $group->increment('contacts_count', $imported);
            }

            $this->reset(['csvFile', 'uploading']);
            $this->dispatch('contacts-uploaded');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->uploading = false;
            session()->flash('error', 'Error al importar contactos: ' . $e->getMessage());
        }
    }

    private function cleanPhoneNumber($phone)
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    public function editContact($contactId)
    {
        $contact = Contact::findOrFail($contactId);

        $this->editingId = $contact->id;
        $this->editPhone = $contact->phone;
        $this->editName = $contact->name;
        $this->editGroupId = $contact->group_id;
        $this->editIsValid = $contact->is_valid;
    }

    public function updateContact()
    {
        $this->validate($this->editRules());

        $contact = Contact::findOrFail($this->editingId);

        $contact->update([
            'phone' => $this->cleanPhoneNumber($this->editPhone),
            'name' => $this->editName,
            'group_id' => $this->editGroupId,
            'is_valid' => $this->editIsValid,
        ]);

        $this->cancelEdit();
        session()->flash('message', 'Contacto actualizado correctamente.');
    }

    public function cancelEdit()
    {
        $this->editingId = null;
        $this->editPhone = '';
        $this->editName = '';
        $this->editGroupId = null;
        $this->editIsValid = false;
    }

    public function deleteContact($contactId)
    {
        $contact = Contact::findOrFail($contactId);

        if ($contact->group_id) {
            $group = Group::find($contact->group_id);
            $group?->decrement('contacts_count');
        }

        $contact->delete();

        session()->flash('message', 'Contacto eliminado correctamente.');
    }

    public function render()
    {
        $groups = Group::orderBy('name')->get();
        $totalContacts = Contact::count();
        $validContacts = Contact::where('is_valid', true)->count();
        $contacts = Contact::with('group')->latest()->paginate(20);

        return view('livewire.contact-upload', [
            'groups' => $groups,
            'totalContacts' => $totalContacts,
            'validContacts' => $validContacts,
            'contacts' => $contacts,
        ]);
    }
}
