<div>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        {{-- Side Nav --}}
        <div class="lg:col-span-1">
            <nav class="space-y-1">
                <a href="{{ route('settings.profile') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('settings.profile') ? 'bg-white text-brand-600 shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Perfil
                </a>
                <a href="{{ route('settings.security') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('settings.security') ? 'bg-white text-brand-600 shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Segurança
                </a>
                <a href="{{ route('settings.company') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('settings.company') ? 'bg-white text-brand-600 shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-7h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Empresa
                </a>
                <a href="{{ route('settings.team') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('settings.team') ? 'bg-white text-brand-600 shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Equipe
                </a>
            </nav>
        </div>

        {{-- Content --}}
        <div class="lg:col-span-3 space-y-6">
            {{-- Invite --}}
            <div class="card p-6">
                <h3 class="font-bold text-slate-900 mb-4">Convidar novo membro</h3>
                <form wire:submit.prevent="invite" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input wire:model="email" type="email" class="input @error('email') input-error @enderror" placeholder="Email do convidado">
                        @error('email') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="w-full md:w-32">
                        <select wire:model="role" class="input">
                            <option value="member">Membro</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary whitespace-nowrap">Enviar Convite</button>
                </form>
            </div>

            {{-- Members List --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="font-bold text-slate-800">Membros da Equipe</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($members as $member)
                    <div class="p-5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <img src="{{ $member->avatar }}" class="w-10 h-10 rounded-full">
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $member->name }} @if($member->id === auth()->id()) <span class="text-[10px] text-brand-600 bg-brand-50 px-1 rounded ml-1 uppercase">Você</span> @endif</p>
                                <p class="text-xs text-slate-400">{{ $member->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded text-slate-500 bg-slate-100">
                                {{ $member->hasRole('admin') ? 'Admin' : 'Membro' }}
                            </span>
                            @if($member->id !== auth()->id())
                            <button wire:click="removeMember({{ $member->id }})" wire:confirm="Remover membro da equipe?" class="text-slate-400 hover:text-red-600 transition-colors">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Pending Invitations --}}
            @if($pendingInvitations->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h3 class="font-bold text-slate-800">Convites Pendentes</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($pendingInvitations as $inv)
                    <div class="p-5 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $inv->email }}</p>
                            <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-0.5">Expira {{ $inv->expires_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded">Aguardando...</span>
                            <button wire:click="cancelInvitation({{ $inv->id }})" class="text-xs text-slate-400 hover:text-red-600 font-bold uppercase tracking-widest">Cancelar</button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>