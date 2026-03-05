<!DOCTYPE html>
<html lang="pt-BR" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MiniCRM — Gerencie clientes e oportunidades com facilidade">
    <title>{{ isset($title) ? $title . ' — MiniCRM' : 'MiniCRM' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="h-full bg-slate-50 font-sans">

    <div class="flex flex-col min-h-screen">
        {{-- Top Navbar --}}
        <header class="w-full h-16 bg-brand-600 border-b border-brand-700/30 sticky top-0 z-40">
            <div class="h-full flex items-center justify-between px-6">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/15 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-white font-bold text-base tracking-tight">MiniCRM</span>
                        <span class="text-blue-100/90 text-sm">Devsquad</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('settings.profile') }}" class="text-sm text-blue-100/90 hover:text-white transition-colors">Configurações</a>
                    <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full ring-2 ring-white/30">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-blue-100/80 hover:text-white transition-colors" title="Sair">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <div class="flex flex-1">
        {{-- Sidebar --}}
        <aside class="w-64 flex-shrink-0 bg-sidebar flex flex-col fixed rounded-r-lg left-0 z-30 top-16 h-[calc(100vh-4rem)]">

            {{-- Nav --}}
            <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
                <p class="px-3 mb-1.5 text-[10px] uppercase tracking-widest text-slate-500 font-semibold">Principal</p>

                <a href="{{ route('dashboard') }}"
                    class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                @can('clients.view')
                    <a href="{{ route('clients.index') }}"
                        class="sidebar-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Clientes
                    </a>
                @endcan

                @can('opportunities.view')
                    <a href="{{ route('opportunities.index') }}"
                        class="sidebar-link {{ request()->routeIs('opportunities.*') ? 'active' : '' }}">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                        </svg>
                        Pipeline
                    </a>
                @endcan

                @can('tasks.view')
                    <a href="{{ route('tasks.index') }}"
                        class="sidebar-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Tarefas
                    </a>
                @endcan

                <div class="pt-3 pb-1">
                    <p class="px-3 mb-1.5 text-[10px] uppercase tracking-widest text-slate-500 font-semibold">Conta</p>
                </div>


                <a href="{{ route('settings.profile') }}"
                    class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Configurações
                </a>
            </nav>

        </aside>

        {{-- Main content --}}
        <div class="flex-1 ml-64 flex flex-col min-h-screen min-w-0">
            {{-- Top bar --}}
            <header class="bg-white border-b border-slate-100 px-6 py-4 flex items-center justify-between sticky top-0 z-20">
                <h1 class="text-lg font-semibold text-slate-800">{{ $title ?? 'MiniCRM' }}</h1>
                <div class="flex items-center gap-3">
                    @if(session('success'))
                    <div class="text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-1.5 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        {{ session('success') }}
                    </div>
                    @endif
                    @if(session('error'))
                    <div class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-3 py-1.5">
                        {{ session('error') }}
                    </div>
                    @endif
                </div>
            </header>

            {{-- Page content --}}
            <main class="flex-1 p-6 min-w-0 overflow-x-hidden">
                {{ $slot }}
            </main>
        </div>
        </div>
    </div>

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    @stack('scripts')
</body>

</html>
