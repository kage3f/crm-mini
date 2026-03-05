<div>
    {{-- Stats grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
        {{-- Total Clients --}}
        <div class="stat-card flex items-center gap-4">
            <div class="w-12 h-12 bg-brand-100 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-900">{{ $totalClients }}</p>
                <p class="text-sm text-slate-500">Clientes</p>
            </div>
        </div>

        {{-- Open Opportunities --}}
        <div class="stat-card flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-900">{{ $openOpportunities }}</p>
                <p class="text-sm text-slate-500">Oportunidades abertas</p>
            </div>
        </div>

        {{-- Estimated Revenue --}}
        <div class="stat-card flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-900">R$ {{ number_format($estimatedRevenue, 0, ',', '.') }}</p>
                <p class="text-sm text-slate-500">Receita prevista</p>
            </div>
        </div>

        {{-- Closed Revenue This Month --}}
        <div class="stat-card flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-900">R$ {{ number_format($closedRevenueThisMonth, 0, ',', '.') }}</p>
                <p class="text-sm text-slate-500">Fechado este mês</p>
            </div>
        </div>
    </div>

    @if($overdueTasksCount > 0)
    <div class="alert-warning mb-6">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Você tem <strong>{{ $overdueTasksCount }} tarefa(s) em atraso</strong>.
            <a href="{{ route('tasks.index') }}?dateFilter=overdue" class="underline font-medium ml-1">Ver tarefas →</a>
        </span>
    </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        {{-- Sales Chart --}}
        <div class="card xl:col-span-2">
            <div class="card-header flex items-center justify-between">
                <h3 class="font-semibold text-slate-800">Vendas nos últimos 6 meses</h3>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="120"></canvas>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-slate-800">Atividade recente</h3>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($recentActivity as $log)
                <div class="px-5 py-3">
                    <p class="text-sm text-slate-700 leading-relaxed">{{ $log->description }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $log->created_at->diffForHumans() }}</p>
                </div>
                @empty
                <div class="px-5 py-8 text-center">
                    <p class="text-sm text-slate-400">Nenhuma atividade ainda</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart');
        if (!ctx) return;

        const data = @json($chartData);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(d => d.month),
                datasets: [{
                    label: 'Receita (R$)',
                    data: data.map(d => d.revenue),
                    backgroundColor: 'rgba(99, 102, 241, 0.8)',
                    borderColor: 'rgba(99, 102, 241, 1)',
                    borderWidth: 0,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => 'R$ ' + v.toLocaleString('pt-BR')
                        },
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
