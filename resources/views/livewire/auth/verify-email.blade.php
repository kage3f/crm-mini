<div class="text-center">
    <div class="w-16 h-16 bg-brand-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
    </div>

    <h2 class="text-2xl font-bold text-slate-900 mb-2">Confirme seu email</h2>
    <p class="text-slate-500 text-sm mb-6">
        Enviamos um link de verificação para <strong>{{ auth()->user()->email }}</strong>.<br>
        Por favor, verifique sua caixa de entrada e spam.
    </p>

    @if (session('success'))
        <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
            <p class="text-sm text-emerald-700">{{ session('success') }}</p>
        </div>
    @endif

    <button wire:click="resendEmail" class="btn-secondary w-full justify-center mb-3" wire:loading.attr="disabled">
        <span wire:loading.remove>Reenviar email de verificação</span>
        <span wire:loading>Reenviando...</span>
    </button>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="text-sm text-slate-400 hover:text-slate-600">Sair e usar outra conta</button>
    </form>
</div>
