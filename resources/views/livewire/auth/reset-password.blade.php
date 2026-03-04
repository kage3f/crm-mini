<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-900">Nova senha</h2>
        <p class="text-slate-500 mt-1">Escolha uma senha segura</p>
    </div>

    <form wire:submit.prevent="resetPassword" class="space-y-4">
        <div>
            <label class="label">Email</label>
            <input wire:model="email" type="email" class="input @error('email') input-error @enderror">
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="label">Nova senha</label>
            <input wire:model="password" type="password" class="input" placeholder="Mínimo 8 caracteres">
        </div>
        <div>
            <label class="label">Confirmar nova senha</label>
            <input wire:model="password_confirmation" type="password" class="input">
        </div>

        <button type="submit" class="btn-primary w-full justify-center" wire:loading.attr="disabled">
            <span wire:loading.remove>Redefinir senha</span>
            <span wire:loading>Salvando...</span>
        </button>
    </form>
</div>