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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen bg-slate-50 font-sans antialiased">

    {{-- Navigation --}}
    <nav class="bg-white/80 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-50 transition-all duration-200 shadow-2xs">
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
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'nav-item-active' : '' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('admin.services.index') }}" class="nav-item {{ request()->routeIs('admin.services.*') ? 'nav-item-active' : '' }}">
                                Services
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'nav-item-active' : '' }}">
                                Users
                            </a>
                            <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings.*') ? 'nav-item-active' : '' }}">
                                Settings
                            </a>
                            <a href="{{ route('admin.audit.index') }}" class="nav-item {{ request()->routeIs('admin.audit.*') ? 'nav-item-active' : '' }}">
                                Audit
                            </a>
                            <a href="{{ route('display') }}" target="_blank" class="nav-item text-indigo-600 font-semibold flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                TV Screen
                            </a>
                            <a href="{{ route('docs') }}" class="nav-item {{ request()->routeIs('docs') ? 'nav-item-active' : '' }}">
                                Docs
                            </a>
                        @elseif(auth()->user()->isStaff())
                            <a href="{{ route('staff.dashboard') }}" class="nav-item {{ request()->routeIs('staff.*') ? 'nav-item-active' : '' }}">
                                Queue Management
                            </a>
                            <a href="{{ route('display') }}" target="_blank" class="nav-item text-indigo-600 font-semibold flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                TV Screen
                            </a>
                            <a href="{{ route('docs') }}" class="nav-item {{ request()->routeIs('docs') ? 'nav-item-active' : '' }}">
                                Docs
                            </a>
                        @else
                            <a href="{{ route('patient.dashboard') }}" class="nav-item {{ request()->routeIs('patient.dashboard') ? 'nav-item-active' : '' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('patient.queue.index') }}" class="nav-item {{ request()->routeIs('patient.queue.*') ? 'nav-item-active' : '' }}">
                                Join Queue
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
                        @endif
                    @else
                        <a href="{{ route('docs') }}" class="nav-item {{ request()->routeIs('docs') ? 'nav-item-active' : '' }}">
                            Docs & Architecture
                        </a>
                        <a href="{{ route('display') }}" target="_blank" class="nav-item text-indigo-600 font-semibold flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Live Screen
                        </a>
                    @endauth
                </div>

                {{-- Right side --}}
                <div class="flex items-center gap-3">
                    @auth
                        {{-- Notification Bell --}}
                        @if(auth()->user()->isPatient())
                            @php $unread = auth()->user()->unreadNotificationsCount(); @endphp
                            <a href="{{ route('patient.dashboard') }}" class="relative p-2 rounded-lg text-slate-500 hover:bg-slate-100 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                @if($unread > 0)
                                    <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-rose-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $unread > 9 ? '9+' : $unread }}</span>
                                @endif
                            </a>
                        @endif

                        {{-- User Menu --}}
                        <div class="flex items-center gap-2.5">
                            <div class="hidden sm:block text-right">
                                <p class="text-sm font-medium text-slate-900 leading-tight">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-500">{{ auth()->user()->role_label }}</p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Sign In</a>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Get Started</a>
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

    @if($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="alert alert-error" role="alert">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
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

    @stack('scripts')
</body>
</html>
