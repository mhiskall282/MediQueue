<x-layouts.app title="Smart Clinic Queue Management">
    {{-- Hero Section --}}
    <section class="bg-gradient-to-br from-indigo-950 via-indigo-900 to-indigo-800 text-white relative overflow-hidden">
        {{-- Grid overlay --}}
        <div class="absolute inset-0 opacity-[0.07]">
            <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid" width="60" height="60" patternUnits="userSpaceOnUse">
                        <path d="M 60 0 L 0 0 0 60" fill="none" stroke="white" stroke-width="1"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>
        </div>

        {{-- Glow orbs --}}
        <div class="absolute top-1/4 -left-24 w-96 h-96 bg-indigo-500 rounded-full opacity-20 blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-80 h-80 bg-violet-600 rounded-full opacity-20 blur-3xl"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 rounded-full px-4 py-1.5 mb-6 text-sm text-indigo-200 backdrop-blur-sm">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    Smart Queue Management — Now Available
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight text-balance mb-6">
                    No more waiting in
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 to-violet-300">physical queues</span>
                </h1>

                <p class="text-lg sm:text-xl text-indigo-200 leading-relaxed mb-10 max-w-2xl">
                    MediQueue lets patients join clinic queues digitally, track their position in real time, and be notified when it's their turn — from anywhere.
                </p>

                <div class="flex flex-col sm:flex-row gap-4">
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-xl bg-white text-indigo-700 hover:bg-indigo-50 shadow-lg shadow-indigo-900/30">
                            Get Started Free
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-xl bg-white/10 text-white border border-white/20 hover:bg-white/20 backdrop-blur-sm">
                            Sign In
                        </a>
                    @else
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-xl bg-white text-indigo-700 hover:bg-indigo-50 shadow-lg">
                                Admin Dashboard
                            </a>
                        @elseif(auth()->user()->isStaff())
                            <a href="{{ route('staff.dashboard') }}" class="btn btn-xl bg-white text-indigo-700 hover:bg-indigo-50 shadow-lg">
                                Staff Dashboard
                            </a>
                        @else
                            <a href="{{ route('patient.dashboard') }}" class="btn btn-xl bg-white text-indigo-700 hover:bg-indigo-50 shadow-lg">
                                My Dashboard
                            </a>
                            <a href="{{ route('patient.queue.index') }}" class="btn btn-xl bg-white/10 text-white border border-white/20 hover:bg-white/20">
                                Join a Queue
                            </a>
                        @endif
                    @endguest
                </div>

                {{-- Stats --}}
                <div class="mt-14 grid grid-cols-3 gap-6 max-w-lg">
                    @foreach([
                        ['5', 'Clinic services'],
                        ['Real-time', 'Position tracking'],
                        ['Zero wait', 'At reception'],
                    ] as $stat)
                        <div>
                            <p class="text-2xl font-bold text-white">{{ $stat[0] }}</p>
                            <p class="text-xs text-indigo-300 mt-0.5">{{ $stat[1] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- How it Works --}}
    <section class="py-20 lg:py-24 bg-white" id="how-it-works" aria-labelledby="how-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 id="how-heading" class="text-3xl sm:text-4xl font-bold text-slate-900 mb-3">How MediQueue works</h2>
                <p class="text-slate-500 text-lg max-w-xl mx-auto">Three simple steps to a stress-free clinic visit.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">
                @foreach([
                    [
                        'step'  => '01',
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
                        'title' => 'Register & Sign In',
                        'desc'  => 'Create a free patient account in under 30 seconds. Your information is securely stored and never shared with third parties.',
                        'color' => 'bg-indigo-50 text-indigo-600',
                        'border'=> 'border-indigo-100',
                    ],
                    [
                        'step'  => '02',
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>',
                        'title' => 'Join a Queue',
                        'desc'  => 'Select your clinic service and join the queue digitally. You\'ll receive a unique queue number instantly — no paper ticket needed.',
                        'color' => 'bg-emerald-50 text-emerald-600',
                        'border'=> 'border-emerald-100',
                    ],
                    [
                        'step'  => '03',
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>',
                        'title' => 'Get Notified',
                        'desc'  => 'Monitor your real-time position and estimated wait. Receive an instant notification when it\'s your turn — so you can relax until then.',
                        'color' => 'bg-amber-50 text-amber-600',
                        'border'=> 'border-amber-100',
                    ],
                ] as $item)
                    <div class="relative">
                        <div class="card p-8 h-full hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-start gap-4 mb-5">
                                <div class="w-12 h-12 {{ $item['color'] }} rounded-xl flex items-center justify-center flex-shrink-0 border {{ $item['border'] }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
                                </div>
                                <span class="text-4xl font-extrabold text-slate-100 mt-1 select-none">{{ $item['step'] }}</span>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ $item['title'] }}</h3>
                            <p class="text-slate-500 text-sm leading-relaxed">{{ $item['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Features Grid --}}
    <section class="py-20 bg-slate-50" aria-labelledby="features-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 id="features-heading" class="text-3xl font-bold text-slate-900 mb-3">Everything a modern clinic needs</h2>
                <p class="text-slate-500 max-w-lg mx-auto">Purpose-built for outpatient clinics — covering patients, staff, and administrators.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach([
                    ['🏃', 'Real-time Queue Position', 'Patients always know exactly where they are in the queue and how long they\'ll wait.'],
                    ['📟', 'Instant Queue Tickets', 'Unique, human-readable ticket numbers (e.g. GC-007) generated atomically with zero duplicates.'],
                    ['👩‍⚕️', 'Staff Operations Console', 'Clinical staff can call, serve, complete, skip, and recall patients from a focused dashboard.'],
                    ['🔑', 'Role-based Access', 'Separate, secured portals for patients, clinical staff, and system administrators.'],
                    ['📋', 'Immutable Audit Log', 'Every action is recorded with actor, timestamp, and context — for full accountability.'],
                    ['⚙️', 'Service Configuration', 'Admins can add, edit, activate, and deactivate clinic services without any downtime.'],
                ] as $feature)
                    <div class="card p-6 flex gap-4 hover:shadow-md transition-shadow">
                        <span class="text-2xl flex-shrink-0">{{ $feature[0] }}</span>
                        <div>
                            <h3 class="font-semibold text-slate-900 mb-1.5">{{ $feature[1] }}</h3>
                            <p class="text-sm text-slate-500 leading-relaxed">{{ $feature[2] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-20 bg-indigo-600" aria-labelledby="cta-heading">
        <div class="max-w-3xl mx-auto text-center px-4">
            <h2 id="cta-heading" class="text-3xl font-bold text-white mb-4">Ready to modernise your clinic queue?</h2>
            <p class="text-indigo-200 text-lg mb-8">Get started in seconds. No credit card required.</p>
            @guest
                <a href="{{ route('register') }}" class="btn btn-xl bg-white text-indigo-700 hover:bg-indigo-50 shadow-lg shadow-indigo-900/30">
                    Create Free Account
                </a>
            @else
                @if(auth()->user()->isPatient())
                    <a href="{{ route('patient.queue.index') }}" class="btn btn-xl bg-white text-indigo-700 hover:bg-indigo-50">
                        Join a Queue Now
                    </a>
                @endif
            @endguest
        </div>
    </section>
</x-layouts.app>
