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
                @can('manage-permissions')
                    <a href="{{ route('settings.permissions') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('settings.permissions') ? 'bg-white text-brand-600 shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5-1a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Permissões
                    </a>
                @endcan
            </nav>
        </div>

        {{-- Content --}}
        <div class="lg:col-span-3 space-y-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="font-bold text-slate-800">Meus Dados</h3>
                </div>
                <form wire:submit.prevent="save" class="p-6 space-y-5">
                    <div class="flex items-center gap-6 mb-2">
                        <img src="{{ $avatar ? $avatar->temporaryUrl() : auth()->user()->avatar }}" class="w-20 h-20 rounded-2xl ring-4 ring-slate-100 object-cover">
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">Membro desde {{ auth()->user()->created_at->format('M Y') }}</p>
                            <label for="avatar-upload" class="btn-ghost btn-sm -ml-2 text-brand-600 mt-1 cursor-pointer">Alterar foto</label>
                            <input id="avatar-upload" type="file" wire:model="avatar" accept="image/jpeg,image/png,image/webp" class="hidden">
                            <p class="text-xs text-slate-400 mt-1">JPG, PNG ou WEBP (max 2MB)</p>
                            @error('avatar') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="label">Nome</label>
                            <input wire:model="name" type="text" class="input @error('name') input-error @enderror">
                            @error('name') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="label">Email</label>
                            <input wire:model="email" type="email" class="input @error('email') input-error @enderror">
                            @error('email') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-50 flex justify-end">
                        <button type="submit" class="btn-primary">Atualizar Perfil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
