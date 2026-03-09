<div>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        {{-- Side Nav --}}
        @include('livewire.settings.partials.nav')

        {{-- Content --}}
        <div class="lg:col-span-3 space-y-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="font-bold text-slate-800">Alterar Senha</h3>
                </div>
                <form wire:submit.prevent="changePassword" class="p-6 space-y-5">
                    <div class="max-w-md space-y-4">
                        <div>
                            <label class="label">Senha Atual</label>
                            <input wire:model="current_password" type="password" class="input @error('current_password') input-error @enderror">
                            @error('current_password') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="label">Nova Senha</label>
                            <input wire:model="new_password" type="password" class="input @error('new_password') input-error @enderror">
                            @error('new_password') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="label">Confirmar Nova Senha</label>
                            <input wire:model="new_password_confirmation" type="password" class="input">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-50 flex justify-end">
                        <button type="submit" class="btn-primary">Alterar Senha</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
