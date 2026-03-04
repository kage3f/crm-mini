<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-900">Recuperar senha</h2>
        <p class="text-slate-500 mt-1">Enviaremos um link para o seu email</p>
    </div>

    @if(session('success'))
    <div class="alert-success mb-5">
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
        {{ session('success') }}
    </div>
    @endif

    <form wire:submit.prevent="sendResetLink" class="space-y-4">
        <div>
            <label class="label" for="email">Email</label>
            <input wire:model="email" id="email" type="email" class="input @error('email') input-error @enderror" placeholder="voce@empresa.com">
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="btn-primary w-full justify-center" wire:loading.attr="disabled">
            <span wire:loading.remove>Enviar link de recuperação</span>
            <span wire:loading>Enviando...</span>
        </button>
    </form>

    <p class="text-center text-sm text-slate-500 mt-5">
        <a href="{{ route('login') }}" class="text-brand-600 font-medium hover:text-brand-700">← Voltar para o login</a>
    </p>
</div>