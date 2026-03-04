<div>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-slate-800">Planos e Preços</h2>
        <p class="text-slate-500 mt-1">Escolha o plano ideal para o tamanho do seu negócio.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @foreach($plans as $plan)
        <div class="card flex flex-col {{ $currentPlan?->id === $plan->id ? 'ring-2 ring-brand-500' : '' }}">
            @if($currentPlan?->id === $plan->id)
            <div class="bg-brand-500 text-white text-[10px] uppercase font-bold text-center py-1 tracking-widest">Seu plano atual</div>
            @endif
            <div class="p-8 flex-1">
                <h3 class="text-xl font-bold text-slate-900 mb-2">{{ $plan->name }}</h3>
                <div class="flex items-baseline gap-1 mb-6">
                    <span class="text-3xl font-bold text-slate-900">R$ {{ number_format($plan->price_monthly, 0, ',', '.') }}</span>
                    <span class="text-slate-500 text-sm">/mês</span>
                </div>

                <ul class="space-y-4 mb-8 text-sm">
                    <li class="flex items-center gap-2.5 text-slate-600">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ $plan->client_limit ? "Até {$plan->client_limit} clientes" : "Clientes ilimitados" }}
                    </li>
                    <li class="flex items-center gap-2.5 text-slate-600">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ $plan->user_limit ? "Até {$plan->user_limit} usuários" : "Usuários ilimitados" }}
                    </li>
                    @if($plan->has_kanban)
                    <li class="flex items-center gap-2.5 text-slate-600">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        Pipeline de Vendas (Kanban)
                    </li>
                    @endif
                    @if($plan->has_tasks)
                    <li class="flex items-center gap-2.5 text-slate-600">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        Gestão de Tarefas
                    </li>
                    @endif
                </ul>
            </div>

            <div class="px-8 pb-8 pt-0 mt-auto">
                @if($currentPlan?->id === $plan->id)
                <button class="btn-secondary w-full justify-center opacity-50 cursor-not-allowed" disabled>Inscrito</button>
                @else
                <a href="{{ route('billing.checkout', $plan->slug) }}" class="btn-primary w-full justify-center">
                    {{ $plan->isFree() ? 'Começar grátis' : 'Fazer Upgrade' }}
                </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-12 bg-slate-100 rounded-2xl p-8 border border-slate-200">
        <div class="flex flex-col md:flex-row items-center gap-6">
            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-brand-600 shadow-sm shrink-0">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <div class="flex-1 text-center md:text-left">
                <h4 class="text-lg font-bold text-slate-800">Pagamento Seguro com Stripe</h4>
                <p class="text-slate-600 text-sm mt-1">Utilizamos a infraestrutura do Stripe para garantir que seus dados de cobrança estejam sempre seguros e criptografados.</p>
            </div>
        </div>
    </div>
</div>