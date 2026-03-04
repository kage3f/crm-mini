<div>
    <div class="mb-8">
        <a href="{{ route('billing.plans') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 flex items-center gap-1.5 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Voltar para planos
        </a>
        <h2 class="text-2xl font-bold text-slate-800">Finalizar Assinatura</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        {{-- Checkout Form --}}
        <div class="space-y-6">
            <div class="card p-6">
                <form method="POST" action="{{ route('billing.simulate') }}">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">

                    <h3 class="font-bold text-slate-900 mb-5 pb-4 border-b border-slate-50">Dados de Pagamento (Simulação)</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="label">Nome no cartão</label>
                            <input name="card_name" type="text" class="input" placeholder="JOAO S SILVA" required>
                        </div>
                        <div>
                            <label class="label">Número do cartão</label>
                            <div class="relative">
                                <input name="card_number" type="text" class="input pr-12" placeholder="0000 0000 0000 0000" maxlength="16" required>
                                <span class="absolute right-3 inset-y-0 flex items-center text-slate-300">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M1 4h22v16H1zM3 6v12h18V6H3zm2 2h4v2H5V8zm0 4h10v2H5v-2z" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label">Validade (MM/AA)</label>
                                <input name="card_expiry" type="text" class="input" placeholder="12/28" maxlength="5" required>
                            </div>
                            <div>
                                <label class="label">CVV</label>
                                <input name="card_cvv" type="text" class="input" placeholder="123" maxlength="4" required>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <button type="submit" class="btn-primary w-full justify-center py-3 text-base shadow-lg shadow-brand-500/20">
                            Confirmar Assinatura - R$ {{ number_format($plan->price_monthly, 2, ',', '.') }}
                        </button>
                        <p class="text-[10px] text-slate-400 text-center mt-3 uppercase tracking-tighter">
                            🔒 Pagamento processado de forma segura pelo Stripe
                        </p>
                    </div>
                </form>
            </div>
        </div>

        {{-- Summary --}}
        <div>
            <div class="card bg-brand-950 text-white border-0 p-8 shadow-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
                    </svg>
                </div>

                <h3 class="text-brand-300 uppercase tracking-widest text-[10px] font-bold mb-1">Resumo do pedido</h3>
                <p class="text-2xl font-bold mb-6">Plano {{ $plan->name }}</p>

                <div class="space-y-4 mb-8">
                    @if($plan->client_limit)
                    <div class="flex items-center gap-3 text-sm">
                        <svg class="w-4.5 h-4.5 text-brand-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Até {{ $plan->client_limit }} clientes</span>
                    </div>
                    @else
                    <div class="flex items-center gap-3 text-sm">
                        <svg class="w-4.5 h-4.5 text-brand-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Clientes ilimitados</span>
                    </div>
                    @endif
                    <div class="flex items-center gap-3 text-sm">
                        <svg class="w-4.5 h-4.5 text-brand-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Suporte prioritário via email</span>
                    </div>
                </div>

                <div class="border-t border-white/10 pt-6">
                    <div class="flex items-center justify-between text-lg font-bold">
                        <span>Total hoje</span>
                        <span>R$ {{ number_format($plan->price_monthly, 2, ',', '.') }}</span>
                    </div>
                    <p class="text-xs text-brand-400 mt-1">Cobrança recorrente mensal</p>
                </div>
            </div>

            <div class="mt-6 flex items-start gap-4 p-4 rounded-xl bg-orange-50 border border-orange-100">
                <svg class="w-5 h-5 text-orange-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-xs text-orange-800 leading-relaxed">
                    <strong>Ambiente de Simulação:</strong> Este formulário é apenas para fins de demonstração do fluxo de checkout. Nenhuma cobrança real será feita e você pode preencher com dados fictícios.
                </div>
            </div>
        </div>
    </div>
</div>