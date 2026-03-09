<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    public string $name  = '';
    public string $email = '';
    public $avatar;

    public function mount(): void
    {
        $this->name  = auth()->user()->name;
        $this->email = auth()->user()->email;
    }

    protected function rules(): array
    {
        return [
            'name'  => 'required|string|min:2|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'avatar' => 'nullable|image|max:2048|mimes:jpg,jpeg,png,webp',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $user = auth()->user();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->avatar) {
            if ($user->avatar_url && !str_starts_with($user->avatar_url, 'http://') && !str_starts_with($user->avatar_url, 'https://') && !str_starts_with($user->avatar_url, '/')) {
                Storage::disk('public')->delete($user->avatar_url);
            }

            $data['avatar_url'] = $this->avatar->store('avatars', 'public');
        }

        $user->update($data);
        $this->reset('avatar');

        session()->flash('success', 'Perfil atualizado com sucesso!');
    }

    public function render()
    {
        return view('livewire.settings.profile')
            ->layout('layouts.app', ['title' => 'Perfil']);
    }
}
