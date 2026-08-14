<x-layouts.app title="Smart Hospital Outpatient & Emergency Queue Management System">
    {{-- Hero Section --}}
    <div class="relative overflow-hidden bg-gradient-to-b from-indigo-950 via-slate-900 to-slate-950 text-white py-16 sm:py-24 border-b border-slate-800">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-indigo-500/20 via-transparent to-transparent"></div>
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                {{-- Left Text & CTAs --}}
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Enterprise Clinical Queue Intelligence & Emergency Telemetry
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-white leading-none">
                        Eliminate Waiting Lines. <br/>
                        <span class="bg-gradient-to-r from-indigo-400 via-indigo-200 to-emerald-400 bg-clip-text text-transparent">
                            Deliver Faster Care.
                        </span>
                    </h1>

                    <p class="text-base sm:text-lg text-slate-300 max-w-2xl font-normal leading-relaxed">
                        MediQueue transforms chaotic hospital waiting rooms into a synchronized digital care ecosystem. Features real-time SMS/email alerts, 5-tier Manchester triage, ward bed allocation, diagnostic lab loopbacks, and 4K waiting room TV screens.
                    </p>

                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        @auth
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-6 py-3 text-sm shadow-lg shadow-indigo-600/30">
                                    📊 Launch Admin Dashboard &rarr;
                                </a>
                            @elseif(auth()->user()->isStaff())
                                <a href="{{ route('staff.dashboard') }}" class="btn btn-primary bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-6 py-3 text-sm shadow-lg shadow-indigo-600/30">
                                    🩺 Open Clinical Console &rarr;
                                </a>
                            @else
                                <a href="{{ route('patient.queue.index') }}" class="btn btn-primary bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-6 py-3 text-sm shadow-lg shadow-indigo-600/30">
                                    🎟️ Join Outpatient Queue &rarr;
                                </a>
                            @endif
                        @else
                            <a href="{{ route('register') }}" class="btn btn-primary bg-indigo-600 hover:bg-indigo-500 text-white font-black px-7 py-3.5 text-sm shadow-xl shadow-indigo-600/30">
                                🎟️ Join Virtual Queue Now
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-secondary bg-white/10 hover:bg-white/20 text-white border-white/20 font-bold px-6 py-3.5 text-sm backdrop-blur-sm">
                                🔑 Staff & Patient Log In
                            </a>
                        @endauth

                        <a href="{{ route('display') }}" target="_blank" class="btn btn-secondary bg-emerald-500/20 text-emerald-300 border-emerald-500/30 hover:bg-emerald-500/30 text-sm font-bold flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            📺 Hospital TV Screen
                        </a>
                    </div>
                </div>

                {{-- Right Interactive Live Mockup Card --}}
                <div class="lg:col-span-5">
                    <div class="bg-slate-900/90 border-2 border-indigo-500/40 rounded-3xl p-6 shadow-2xl shadow-indigo-950 backdrop-blur-xl space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                <span class="text-xs font-mono text-slate-400 ml-2">LIVE OUTPATIENT FEED</span>
                            </div>
                            <span class="text-[10px] font-mono text-emerald-400 font-bold">SYNCHRONIZED</span>
                        </div>

                        {{-- Active Called Box --}}
                        <div class="p-5 bg-gradient-to-br from-indigo-900/60 to-slate-900 border border-indigo-500/40 rounded-2xl text-center">
                            <span class="text-[10px] font-black uppercase tracking-wider text-indigo-300 block mb-1">NOW CALLING</span>
                            <span class="text-5xl font-black font-mono text-white block">GC-003</span>
                            <span class="text-xs font-bold text-indigo-200 block mt-1">General Consultation &bull; Room 2</span>
                            <span class="text-[11px] text-slate-400 block">Dr. Sarah Ahmad &bull; In Consultation</span>
                        </div>

                        {{-- Next in Queue list --}}
                        <div class="space-y-2">
                            <div class="flex items-center justify-between p-3 bg-slate-800/60 rounded-xl text-xs">
                                <div class="flex items-center gap-2.5">
                                    <span class="font-bold text-indigo-400 font-mono">EMG-001</span>
                                    <span class="text-slate-200">Trauma Resuscitation</span>
                                </div>
                                <span class="badge bg-red-600 text-white text-[9px] font-bold">RED (P1)</span>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-slate-800/60 rounded-xl text-xs">
                                <div class="flex items-center gap-2.5">
                                    <span class="font-bold text-indigo-400 font-mono">LAB-004</span>
                                    <span class="text-slate-200">Diagnostic Blood Panel</span>
                                </div>
                                <span class="badge bg-orange-600 text-white text-[9px] font-bold">ORANGE (P2)</span>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-slate-800/60 rounded-xl text-xs">
                                <div class="flex items-center gap-2.5">
                                    <span class="font-bold text-indigo-400 font-mono">PH-012</span>
                                    <span class="text-slate-200">Pharmacy Pickup</span>
                                </div>
                                <span class="badge bg-emerald-600 text-white text-[9px] font-bold">GREEN (P4)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- System Core Features Grid --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 block mb-2">Hospital Grade Infrastructure</span>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Engineered for High-Throughput Clinical Workflows</h2>
            <p class="text-sm text-slate-500 mt-2">Comprehensive suite of clinical triage, appointment scheduling, diagnostic loops, and hospital ward tracking.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="card p-6 border-l-4 border-red-500 hover:shadow-md transition-shadow">
                <div class="text-3xl mb-3">🚨</div>
                <h3 class="text-base font-black text-slate-900">5-Tier Manchester Emergency Triage</h3>
                <p class="text-xs text-slate-600 mt-2">
                    Prioritizes patients automatically across 5 color-coded acuity levels (Red, Orange, Yellow, Green, Blue) with immediate trauma Code Red escalations.
                </p>
            </div>

            <div class="card p-6 border-l-4 border-indigo-500 hover:shadow-md transition-shadow">
                <div class="text-3xl mb-3">🧪</div>
                <h3 class="text-base font-black text-slate-900">Lab Referral & Auto-Loopback</h3>
                <p class="text-xs text-slate-600 mt-2">
                    Doctors order lab/radiology investigations and transfer tickets seamlessly. When tests complete, the system automatically routes the patient back to the doctor for review.
                </p>
            </div>

            <div class="card p-6 border-l-4 border-purple-500 hover:shadow-md transition-shadow">
                <div class="text-3xl mb-3">🛏️</div>
                <h3 class="text-base font-black text-slate-900">Ward & Bed Allocation Engine</h3>
                <p class="text-xs text-slate-600 mt-2">
                    Real-time hospital bed capacity management across Emergency Bays, Observation Wards, and ICU, with automatic release upon patient discharge.
                </p>
            </div>

            <div class="card p-6 border-l-4 border-emerald-500 hover:shadow-md transition-shadow">
                <div class="text-3xl mb-3">👨‍⚕️</div>
                <h3 class="text-base font-black text-slate-900">Doctor On-Call Rostering & Paging</h3>
                <p class="text-xs text-slate-600 mt-2">
                    Live clinician shift scheduling (Day, Night, Trauma Cover) and single-click emergency clinical paging with instant high-priority alerts.
                </p>
            </div>

            <div class="card p-6 border-l-4 border-blue-500 hover:shadow-md transition-shadow">
                <div class="text-3xl mb-3">📅</div>
                <h3 class="text-base font-black text-slate-900">Advance Clinic Appointments</h3>
                <p class="text-xs text-slate-600 mt-2">
                    Patient self-scheduling portal with duplicate booking guards, doctor pre-consultation messaging, and instant reception check-in desk.
                </p>
            </div>

            <div class="card p-6 border-l-4 border-amber-500 hover:shadow-md transition-shadow">
                <div class="text-3xl mb-3">📈</div>
                <h3 class="text-base font-black text-slate-900">Executive PDF & CSV Analytics</h3>
                <p class="text-xs text-slate-600 mt-2">
                    Download streaming CSV datasets, print-ready executive clinical PDF reports, and inspect complete forensic audit trails with client IP addresses.
                </p>
            </div>
        </div>
    </div>

    {{-- Ready-to-Test Credentials Section --}}
    <div class="bg-slate-100 py-16 border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-700 block mb-1">Interactive Evaluation Sandbox</span>
                <h2 class="text-2xl font-black text-slate-900">Pre-Configured Demonstration Accounts</h2>
                <p class="text-xs text-slate-500 mt-1">Test each role instantly with the pre-seeded credentials below (Password for all: <code class="bg-white px-2 py-0.5 rounded font-mono font-bold text-indigo-700">password</code>):</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 max-w-4xl mx-auto">
                {{-- Admin Card --}}
                <div class="card p-5 bg-white border border-slate-200 shadow-sm text-center">
                    <div class="w-10 h-10 bg-indigo-100 text-indigo-700 rounded-xl flex items-center justify-center font-bold text-lg mx-auto mb-3">
                        🏢
                    </div>
                    <span class="badge bg-indigo-100 text-indigo-800 text-[10px] font-bold">ADMINISTRATOR</span>
                    <h3 class="font-bold text-slate-900 text-sm mt-2">System Admin</h3>
                    <p class="font-mono text-xs text-slate-600 bg-slate-50 p-2 rounded-lg mt-2 border">admin@mediqueue.test</p>
                    <a href="{{ route('login') }}" class="btn btn-secondary btn-sm text-xs font-bold w-full mt-4">
                        Log In as Admin &rarr;
                    </a>
                </div>

                {{-- Doctor Card --}}
                <div class="card p-5 bg-white border border-slate-200 shadow-sm text-center">
                    <div class="w-10 h-10 bg-emerald-100 text-emerald-700 rounded-xl flex items-center justify-center font-bold text-lg mx-auto mb-3">
                        👨‍⚕️
                    </div>
                    <span class="badge bg-emerald-100 text-emerald-800 text-[10px] font-bold">CLINICAL STAFF</span>
                    <h3 class="font-bold text-slate-900 text-sm mt-2">Dr. Sarah Ahmad</h3>
                    <p class="font-mono text-xs text-slate-600 bg-slate-50 p-2 rounded-lg mt-2 border">dr.sarah@mediqueue.test</p>
                    <a href="{{ route('login') }}" class="btn btn-secondary btn-sm text-xs font-bold w-full mt-4">
                        Log In as Doctor &rarr;
                    </a>
                </div>

                {{-- Patient Card --}}
                <div class="card p-5 bg-white border border-slate-200 shadow-sm text-center">
                    <div class="w-10 h-10 bg-blue-100 text-blue-700 rounded-xl flex items-center justify-center font-bold text-lg mx-auto mb-3">
                        👤
                    </div>
                    <span class="badge bg-blue-100 text-blue-800 text-[10px] font-bold">PATIENT</span>
                    <h3 class="font-bold text-slate-900 text-sm mt-2">John Doe</h3>
                    <p class="font-mono text-xs text-slate-600 bg-slate-50 p-2 rounded-lg mt-2 border">john.doe@example.com</p>
                    <a href="{{ route('login') }}" class="btn btn-secondary btn-sm text-xs font-bold w-full mt-4">
                        Log In as Patient &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
