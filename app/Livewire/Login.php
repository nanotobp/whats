<?php

namespace App\Livewire;

use Livewire\Component;

class Login extends Component
{
    public $email = '';
    public $password = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ];

    public function login()
    {
        $this->validate();

        if ($this->email === 'mensaje@demo.com' && $this->password === 'admin123') {
            session(['authenticated' => true, 'user_email' => $this->email]);
            return redirect()->route('dashboard');
        }

        session()->flash('error', 'Credenciales incorrectas.');
    }

    public function render()
    {
        return view('livewire.login')->layout('components.layouts.guest');
    }
}
