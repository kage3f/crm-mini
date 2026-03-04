<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-900">Bem-vindo de volta</h2>
        <p class="text-slate-500 mt-1">Entre com sua conta MiniCRM</p>
    </div>

    <form wire:submit.prevent="login" class="space-y-4">
        <div>
            <label class="label" for="email">Email</label>
            <input wire:model="email" id="email" type="email" class="input @error('email') input-error @enderror"
                placeholder="voce@empresa.com" autocomplete="email">
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label class="label !mb-0" for="password">Senha</label>
                <a href="{{ route('password.request') }}" class="text-xs text-brand-600 hover:text-brand-700 font-medium">
                    Esqueci minha senha
                </a>
            </div>
            <input wire:model="password" id="password" type="password" class="input" placeholder="••••••••" autocomplete="current-password">
        </div>

        <div class="flex items-center gap-2">
            <input wire:model="remember" id="remember" type="checkbox" class="rounded text-brand-600 focus:ring-brand-500">
            <label for="remember" class="text-sm text-slate-600">Manter conectado</label>
        </div>

        <button type="submit" class="btn-primary w-full justify-center" wire:loading.attr="disabled">
            <span wire:loading.remove>Entrar</span>
            <span wire:loading>Entrando...</span>
        </button>
    </form>

    <p class="text-center text-sm text-slate-500 mt-5">
        Não tem uma conta?
        <a href="{{ route('register') }}" class="text-brand-600 font-medium hover:text-brand-700">Cadastre-se grátis</a>
    </p>
</div>