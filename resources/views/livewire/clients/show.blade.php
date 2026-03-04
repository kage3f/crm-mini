<div>
    <div class="flex items-start justify-between mb-8">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 bg-brand-100 rounded-2xl flex items-center justify-center text-brand-600 shrink-0">
                <span class="text-2xl font-bold">{{ substr($client->name, 0, 1) }}</span>
            </div>
            <div>
                <nav class="flex text-xs font-medium text-slate-400 mb-1" aria-label="Breadcrumb">
                    <ol class="flex items-center gap-1">
                        <li><a href="{{ route('clients.index') }}" class="hover:text-brand-600 transition-colors">Clientes</a></li>
                        <li><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg></li>
                        <li class="text-slate-600">Detalhes</li>
                    </ol>
                </nav>
                <h2 class="text-2xl font-bold text-slate-900">{{ $client->name }}</h2>
                <div class="flex items-center gap-3 mt-1.5">
                    <span class="{{ $client->status_color }}">{{ $client->status_label }}</span>
                    @if($client->company)
                    <span class="text-sm text-slate-500 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-7h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        {{ $client->company }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="mailto:{{ $client->email }}" class="btn-secondary">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Email
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Info Column --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="font-bold text-slate-800">Sobre</h3>
                </div>
                <div class="card-body">
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-widest font-semibold mb-1">Email</p>
                            <p class="text-sm text-slate-800">{{ $client->email ?? 'Não informado' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-widest font-semibold mb-1">Telefone</p>
                            <p class="text-sm text-slate-800">{{ $client->phone ?? 'Não informado' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-widest font-semibold mb-1">Observações</p>
                            <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $client->notes ?? 'Nenhuma observação cadastrada.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content Column --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Opportunities --}}
            <div class="card">
                <div class="card-header flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 text-base">Oportunidades</h3>
                    <a href="{{ route('opportunities.index') }}" class="text-brand-600 text-sm font-medium hover:underline">Ver todas</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($client->opportunities as $opp)
                    <div class="p-5 flex items-center justify-between hover:bg-slate-50 transition-colors">
                        <div>
                            <h4 class="font-semibold text-slate-900 leading-tight mb-1">{{ $opp->title }}</h4>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-medium px-2 py-0.5 rounded" style="background-color: {{ $opp->stage->color }}20; color: {{ $opp->stage->color }}">
                                    {{ $opp->stage->name }}
                                </span>
                                <span class="text-xs text-slate-400">{{ $opp->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-slate-900">{{ $opp->formatted_value }}</p>
                            @if($opp->expected_close_date)
                            <p class="text-[10px] text-slate-500 uppercase tracking-tighter">Fecha em {{ $opp->expected_close_date->format('d/m') }}</p>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center bg-slate-50/30">
                        <p class="text-sm text-slate-400">Nenhuma oportunidade aberta</p>
                        <a href="{{ route('opportunities.index') }}" class="btn-ghost btn-sm mt-3 text-brand-600">Criar oportunidade</a>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Tasks --}}
            <div class="card">
                <div class="card-header flex items-center justify-between">
                    <h3 class="font-bold text-slate-800">Tarefas recentes</h3>
                    <a href="{{ route('tasks.index') }}" class="text-brand-600 text-sm font-medium hover:underline">Ir para tarefas</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($client->tasks as $task)
                    <div class="p-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full {{ $task->status === 'done' ? 'bg-emerald-500' : 'bg-amber-500' }}"></div>
                            <div>
                                <p class="text-sm font-medium {{ $task->status === 'done' ? 'text-slate-400 line-through' : 'text-slate-800' }}">
                                    {{ $task->title }}
                                </p>
                                @if($task->due_date)
                                <p class="text-xs {{ $task->isOverdue() ? 'text-red-500' : 'text-slate-400' }}">
                                    Prazo: {{ $task->due_date->format('d/m/Y') }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center bg-slate-50/30">
                        <p class="text-sm text-slate-400">Nenhuma tarefa cadastrada</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
