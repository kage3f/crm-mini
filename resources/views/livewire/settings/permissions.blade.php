<div>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        {{-- Side Nav --}}
        @include('livewire.settings.partials.nav')

        {{-- Content --}}
        <div class="lg:col-span-3 space-y-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="font-bold text-slate-800">Permissões por usuário</h3>
                </div>
                <div class="p-6 space-y-6">
                    @foreach($members as $member)
                        <div class="border border-slate-100 rounded-xl p-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $member->avatar }}" class="w-10 h-10 rounded-full">
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">{{ $member->name }} @if($member->id === auth()->id()) <span class="text-[10px] text-brand-600 bg-brand-50 px-1 rounded ml-1 uppercase">Você</span> @endif</p>
                                        <p class="text-xs text-slate-400">{{ $member->email }}</p>
                                    </div>
                                </div>
                                <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded text-slate-500 bg-slate-100">
                                    {{ $member->hasRole('admin') ? 'Admin' : 'Membro' }}
                                </span>
                            </div>

                            @if($member->hasRole('admin'))
                                <p class="text-xs text-slate-400 mt-4">Administradores sempre possuem todas as permissões.</p>
                            @else
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($permissionGroups as $groupLabel => $permissions)
                                        <div class="bg-slate-50 rounded-lg p-4">
                                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">{{ $groupLabel }}</h4>
                                            <div class="space-y-2">
                                                @foreach($permissions as $perm => $label)
                                                    <label class="flex items-center justify-between gap-4 text-sm text-slate-700">
                                                        <span>{{ $label }}</span>
                                                        <input
                                                            type="checkbox"
                                                            class="h-4 w-4"
                                                            @checked($member->hasPermissionTo($perm))
                                                            wire:change="togglePermission({{ $member->id }}, '{{ $perm }}')"
                                                        />
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
