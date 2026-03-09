<div>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        {{-- Side Nav --}}
        @include('livewire.settings.partials.nav')

        {{-- Content --}}
        <div class="lg:col-span-3 space-y-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="font-bold text-slate-800">Meus Dados</h3>
                </div>
                <form wire:submit.prevent="save" class="p-6 space-y-5">
                    <div class="flex items-center gap-6 mb-2">
                        <img src="{{ $avatar ? $avatar->temporaryUrl() : auth()->user()->avatar }}" class="w-20 h-20 rounded-2xl ring-4 ring-slate-100 object-cover">
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">Membro desde {{ auth()->user()->created_at->format('M Y') }}</p>
                            <label for="avatar-upload" class="btn-ghost btn-sm -ml-2 text-brand-600 mt-1 cursor-pointer">Alterar foto</label>
                            <input id="avatar-upload" type="file" wire:model="avatar" accept="image/jpeg,image/png,image/webp" class="hidden">
                            <p class="text-xs text-slate-400 mt-1">JPG, PNG ou WEBP (max 2MB)</p>
                            @error('avatar') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="label">Nome</label>
                            <input wire:model="name" type="text" class="input @error('name') input-error @enderror">
                            @error('name') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="label">Email</label>
                            <input wire:model="email" type="email" class="input @error('email') input-error @enderror">
                            @error('email') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-50 flex justify-end">
                        <button type="submit" class="btn-primary">Atualizar Perfil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
