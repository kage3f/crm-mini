<div class="text-center">
    <div class="w-16 h-16 bg-brand-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
    </div>

    <h2 class="text-2xl font-bold text-slate-900 mb-2">Confirme seu email</h2>
    <p class="text-slate-500 text-sm mb-6">
        Enviamos um link de verificação para <strong>{{ auth()->user()->email }}</strong>.<br>
        Por favor, verifique sua caixa de entrada.
    </p>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn-secondary w-full justify-center mb-3">
            Reenviar email de verificação
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="text-sm text-slate-400 hover:text-slate-600">Sair e usar outra conta</button>
    </form>
</div>