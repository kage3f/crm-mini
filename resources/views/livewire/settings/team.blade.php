<div>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        {{-- Side Nav --}}
        @include('livewire.settings.partials.nav')

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
