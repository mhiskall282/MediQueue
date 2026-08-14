<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $description ?? 'MediQueue — Smart Clinic Queue Management System. Join queues digitally and monitor your position in real time.' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' — MediQueue' : 'MediQueue — Smart Clinic Queue Management' }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏥</text></svg>">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <!-- Chart.js for Visual Analytics & Real-Time KPIs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        /* Smooth custom scrollbar for Left Navigation Sidebar */
        .sidebar-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-scrollbar::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.6);
        }
        .sidebar-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(71, 85, 105, 0.5);
            border-radius: 4px;
        }
        .sidebar-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(99, 102, 241, 0.8);
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 font-sans antialiased text-slate-900">

@if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isStaff()))
    {{-- ============================================================ --}}
    {{-- ADMIN & STAFF LEFT SIDEBAR LAYOUT                           --}}
    {{-- ============================================================ --}}
    <div class="min-h-screen flex flex-col md:flex-row">
        {{-- Mobile Top Bar --}}
        <div class="md:hidden bg-slate-950 text-white p-4 flex items-center justify-between border-b border-slate-800 z-50 sticky top-0">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <div class="w-7 h-7 bg-indigo-600 rounded-lg flex items-center justify-center font-bold text-xs">🏥</div>
                <span class="font-black text-sm tracking-tight">MediQueue</span>
            </a>
            <button onclick="toggleMobileSidebar()" class="p-2 rounded-lg bg-slate-900 text-slate-300 hover:text-white border border-slate-800" aria-label="Toggle Navigation">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        {{-- Mobile Overlay Backdrop --}}
        <div id="mobileBackdrop" onclick="toggleMobileSidebar()" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-40 hidden md:hidden transition-opacity"></div>

        {{-- Left Sidebar --}}
        <aside id="mainSidebar" class="fixed md:sticky top-0 left-0 h-screen w-72 bg-slate-900 text-slate-300 flex-shrink-0 flex flex-col justify-between border-r border-slate-800 z-50 shadow-2xl transition-transform duration-300 -translate-x-full md:translate-x-0">
            <div class="flex flex-col h-full overflow-hidden">
                {{-- Brand Header --}}
                <div class="h-16 px-6 flex items-center justify-between border-b border-slate-800/80 bg-slate-950/60 flex-shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                        <div class="w-9 h-9 bg-gradient-to-tr from-indigo-500 via-indigo-600 to-indigo-700 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-lg font-black text-white tracking-tight block">MediQueue</span>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-400 block -mt-1">
                                {{ auth()->user()->isAdmin() ? 'Hospital Admin' : 'Clinical Console' }}
                            </span>
                        </div>
                    </a>

                    <button onclick="toggleMobileSidebar()" class="md:hidden text-slate-400 hover:text-white p-1">
                        &times;
                    </button>
                </div>

                {{-- Grouped Navigation Menu with Smooth Scrollbar --}}
                <nav class="p-4 space-y-6 overflow-y-auto sidebar-scrollbar flex-1">
                    {{-- 1. Executive & Analytics --}}
                    @if(auth()->user()->isAdmin())
                        <div>
                            <span class="px-3 text-[10px] font-black uppercase tracking-wider text-slate-500 block mb-2">
                                Executive & Analytics
                            </span>
                            <div class="space-y-1">
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                    <span>📊</span> Admin Overview
                                </a>
                                <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                    <span>📈</span> Clinical Reports & KPIs
                                </a>
                                <a href="{{ route('admin.audit.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.audit.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                    <span>🛡️</span> Forensic Audit Trail
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- 2. Clinical Operations --}}
                    <div>
                        <span class="px-3 text-[10px] font-black uppercase tracking-wider text-slate-500 block mb-2">
                            Clinical Operations
                        </span>
                        <div class="space-y-1">
                            <a href="{{ route('staff.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('staff.dashboard') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <span>🩺</span> Clinical Queue Console
                            </a>
                            <a href="{{ route('staff.emergency.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('staff.emergency.*') ? 'bg-rose-600 text-white shadow-sm' : 'text-rose-400 hover:text-rose-200 hover:bg-rose-950/30' }}">
                                <div class="flex items-center gap-2.5">
                                    <span>🚨</span> Emergency Trauma
                                </div>
                                <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-rose-500/20 text-rose-300 border border-rose-500/30">CODE RED</span>
                            </a>
                            <a href="{{ route('staff.oncall.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('staff.oncall.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <span>👨‍⚕️</span> Doctor On-Call Board
                            </a>
                            <a href="{{ route('staff.beds.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('staff.beds.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <span>🛏️</span> Ward & Bed Allocation
                            </a>
                            <a href="{{ route('staff.appointments.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('staff.appointments.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <span>📅</span> Clinic Appointments
                            </a>
                        </div>
                    </div>

                    {{-- 3. Hospital Administration --}}
                    @if(auth()->user()->isAdmin())
                        <div>
                            <span class="px-3 text-[10px] font-black uppercase tracking-wider text-slate-500 block mb-2">
                                Hospital Administration
                            </span>
                            <div class="space-y-1">
                                <a href="{{ route('admin.services.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.services.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                    <span>🏢</span> Service Catalogue
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                    <span>👥</span> Medical Staff & Users
                                </a>
                                <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.settings.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                    <span>⚙️</span> Clinic & System Settings
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- 4. Public Monitors & Docs --}}
                    <div>
                        <span class="px-3 text-[10px] font-black uppercase tracking-wider text-slate-500 block mb-2">
                            Monitors & Architecture
                        </span>
                        <div class="space-y-1">
                            <a href="{{ route('display') }}" target="_blank" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-emerald-400 hover:text-emerald-300 hover:bg-emerald-950/30 transition-all">
                                <div class="flex items-center gap-2.5">
                                    <span>📺</span> Hospital TV Screen
                                </div>
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            </a>
                            <a href="{{ route('docs') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('docs') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <span>📖</span> In-App Docs Hub
                            </a>
                        </div>
                    </div>
                </nav>

                {{-- Sidebar Footer: User Profile & Logout --}}
                <div class="p-4 border-t border-slate-800/80 bg-slate-950/60 flex-shrink-0">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-xs text-white truncate block">{{ auth()->user()->name }}</span>
                                <span class="badge text-[9px] px-1.5 py-0.2 {{ auth()->user()->isAdmin() ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' }}">
                                    {{ strtoupper(auth()->user()->role) }}
                                </span>
                            </div>
                            <span class="text-[10px] font-mono text-slate-400 truncate block">{{ auth()->user()->hospital_id ?? auth()->user()->email }}</span>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-950/30 transition-colors" title="Sign Out">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Right Main Content Area --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-x-hidden">
            {{-- Top Operational Bar --}}
            <header class="h-16 bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-30 px-4 sm:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold text-slate-500">Hospital Operational Portal</span>
                    <span class="text-slate-300">&bull;</span>
                    <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Live System Synchronized
                    </div>
                </div>

                <div class="flex items-center gap-4 text-xs">
                    <span class="hidden sm:inline-block font-mono text-slate-500">{{ date('l, M d, Y') }}</span>
                    <a href="{{ route('display') }}" target="_blank" class="btn btn-secondary btn-sm text-xs font-bold text-indigo-700">
                        <span>📺</span> TV Screen
                    </a>
                </div>
            </header>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                    <div class="alert alert-success" role="alert">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                    <div class="alert alert-error" role="alert">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            {{-- Page Slot --}}
            <main class="flex-1 p-3 sm:p-5 md:p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mainSidebar');
            const backdrop = document.getElementById('mobileBackdrop');
            if (sidebar && backdrop) {
                const isHidden = sidebar.classList.contains('-translate-x-full');
                if (isHidden) {
                    sidebar.classList.remove('-translate-x-full');
                    backdrop.classList.remove('hidden');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    backdrop.classList.add('hidden');
                }
            }
        }
    </script>

@else
    {{-- ============================================================ --}}
    {{-- PATIENT & PUBLIC TOP NAVBAR LAYOUT                           --}}
    {{-- ============================================================ --}}
    <nav class="bg-white/80 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-50 shadow-2xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="w-9 h-9 bg-gradient-to-tr from-indigo-700 via-indigo-600 to-indigo-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md shadow-indigo-500/25 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <span class="text-xl font-black text-slate-900 tracking-tight">MediQueue</span>
                </a>

                {{-- Nav Links --}}
                <div class="hidden md:flex items-center gap-1">
                    @auth
                        <a href="{{ route('patient.dashboard') }}" class="nav-item {{ request()->routeIs('patient.dashboard') ? 'nav-item-active' : '' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('patient.queue.index') }}" class="nav-item {{ request()->routeIs('patient.queue.*') ? 'nav-item-active' : '' }}">
                            Join Queue
                        </a>
                        <a href="{{ route('patient.appointments.index') }}" class="nav-item {{ request()->routeIs('patient.appointments.*') ? 'nav-item-active' : '' }}">
                            Appointments
                        </a>
                        <a href="{{ route('patient.history') }}" class="nav-item {{ request()->routeIs('patient.history') ? 'nav-item-active' : '' }}">
                            History
                        </a>
                        <a href="{{ route('display') }}" target="_blank" class="nav-item text-indigo-600 font-semibold flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Live Board
                        </a>
                        <a href="{{ route('docs') }}" class="nav-item {{ request()->routeIs('docs') ? 'nav-item-active' : '' }}">
                            Docs
                        </a>
                    @else
                        <a href="{{ route('docs') }}" class="nav-item {{ request()->routeIs('docs') ? 'nav-item-active' : '' }}">
                            Docs & Architecture
                        </a>
                        <a href="{{ route('display') }}" target="_blank" class="nav-item text-indigo-600 font-semibold flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            TV Waiting Screen
                        </a>
                    @endauth
                </div>

                {{-- Right Auth Buttons --}}
                <div class="flex items-center gap-3">
                    @auth
                        <div class="hidden sm:flex items-center gap-2 text-right">
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">{{ auth()->user()->name }}</span>
                                <span class="text-[10px] font-mono text-slate-400 block">{{ auth()->user()->hospital_id ?? 'Patient' }}</span>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm text-xs">
                                Sign Out
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-secondary btn-sm text-xs font-bold">
                            Log In
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm text-xs font-bold shadow-xs">
                            Get Started
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="alert alert-success" role="alert">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="alert alert-error" role="alert">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <main>
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="border-t border-slate-200/80 bg-white/70 backdrop-blur-md mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-tr from-indigo-700 to-indigo-600 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-sm font-bold text-slate-900 block">MediQueue</span>
                        <span class="text-[11px] text-slate-400">Smart Clinic Queue System</span>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-6 text-xs text-slate-600 font-semibold">
                    <a href="{{ route('docs') }}" class="hover:text-indigo-600 transition-colors">Documentation & Guides</a>
                    <a href="{{ route('display') }}" target="_blank" class="hover:text-indigo-600 transition-colors flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Hospital TV Screen
                    </a>
                    <a href="https://github.com/mhiskall282/ug-swe-exams" target="_blank" class="hover:text-indigo-600 transition-colors">GitHub Source</a>
                </div>
                <p class="text-xs text-slate-400">
                    Advanced Software Engineering Capstone &copy; {{ date('Y') }}
                </p>
            </div>
        </div>
    </footer>
@endif

    @stack('scripts')
</body>
</html>
