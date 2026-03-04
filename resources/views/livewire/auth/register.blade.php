<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-900">Criar sua conta</h2>
        <p class="text-slate-500 mt-1">Comece grátis, sem cartão de crédito</p>
    </div>

    <form wire:submit.prevent="register" class="space-y-4">
        <div>
            <label class="label" for="name">Nome completo</label>
            <input wire:model="name" id="name" type="text" class="input @error('name') input-error @enderror" placeholder="Seu nome" autocomplete="name">
            @error('name') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label" for="company">Nome da empresa</label>
            <input wire:model="company" id="company" type="text" class="input @error('company') input-error @enderror" placeholder="Sua empresa">
            @error('company') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label" for="email">Email</label>
            <input wire:model="email" id="email" type="email" class="input @error('email') input-error @enderror" placeholder="voce@empresa.com" autocomplete="email">
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label" for="password">Senha</label>
            <input wire:model="password" id="password" type="password" class="input @error('password') input-error @enderror" placeholder="Mínimo 8 caracteres">
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label" for="password_confirmation">Confirmar senha</label>
            <input wire:model="password_confirmation" id="password_confirmation" type="password" class="input" placeholder="Repita a senha">
        </div>

        <button type="submit" class="btn-primary w-full justify-center mt-2" wire:loading.attr="disabled">
            <span wire:loading.remove>Criar conta grátis</span>
            <span wire:loading>Criando...</span>
        </button>
    </form>

    <p class="text-center text-sm text-slate-500 mt-5">
        Já tem uma conta?
        <a href="{{ route('login') }}" class="text-brand-600 font-medium hover:text-brand-700">Entrar</a>
    </p>
</div>