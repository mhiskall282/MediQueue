<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $clinicName }} — Hospital Waiting Room Screen</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=JetBrains+Mono:wght@700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .font-mono-numbers { font-family: 'JetBrains Mono', monospace; }
        @keyframes heroPulse {
            0%, 100% {
                border-color: rgba(99, 102, 241, 0.6);
                box-shadow: 0 0 35px rgba(79, 70, 229, 0.25), inset 0 0 30px rgba(79, 70, 229, 0.15);
            }
            50% {
                border-color: rgba(129, 140, 248, 0.9);
                box-shadow: 0 0 60px rgba(99, 102, 241, 0.45), inset 0 0 50px rgba(99, 102, 241, 0.3);
            }
        }
        .hero-box { animation: heroPulse 3s infinite ease-in-out; }
        
        @keyframes marquee {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        .marquee-track {
            display: inline-block;
            white-space: nowrap;
            animation: marquee 30s linear infinite;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between p-4 sm:p-6 lg:p-8 bg-slate-950 text-white selection:bg-indigo-600">

    {{-- Top Header Board --}}
    <header class="flex items-center justify-between pb-5 border-b border-slate-800/80">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-tr from-indigo-600 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-900/50 border border-indigo-400/30">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">{{ $clinicName }}</h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        LIVE ON-AIR
                    </span>
                </div>
                <p class="text-xs font-semibold uppercase tracking-widest text-indigo-400 mt-0.5">Live Outpatient Queue Display</p>
            </div>
        </div>

        <div class="flex items-center gap-4 sm:gap-6 text-right">
            <div>
                <div id="live-clock" class="text-2xl sm:text-3xl font-black font-mono-numbers tracking-tight text-emerald-400">
                    {{ now()->format('g:i:s A') }}
                </div>
                <div id="live-date" class="text-xs text-slate-400 font-medium">
                    {{ now()->format('l, F j, Y') }}
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button id="soundToggleBtn" onclick="toggleAudio()" class="p-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-white transition-colors text-xs font-bold" title="Toggle Audio Chime">
                    🔔 <span id="soundLabel" class="hidden sm:inline">Chime: ON</span>
                </button>
                <button onclick="toggleFullScreen()" class="p-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-white transition-colors" title="Toggle Fullscreen">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    {{-- Main Screen Grid --}}
    <main class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 my-auto py-6">
        {{-- Left: Big "NOW CALLING" Featured Box (5 cols) --}}
        <div class="lg:col-span-5 flex flex-col justify-center">
            <div class="hero-box bg-gradient-to-b from-slate-900/90 to-indigo-950/40 border-2 border-indigo-500/60 rounded-3xl p-8 sm:p-10 text-center shadow-2xl relative overflow-hidden">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-indigo-500/20 text-indigo-300 border border-indigo-500/40 mb-6">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-400 animate-ping"></span>
                    NOW CALLING FOR CONSULTATION
                </div>

                <div id="lead-number" class="text-7xl sm:text-8xl md:text-9xl font-black font-mono-numbers tracking-tighter text-white drop-shadow-lg">
                    {{ $leadCalled?->queue_number ?? '—' }}
                </div>

                <div id="lead-department" class="text-xl sm:text-2xl font-bold text-indigo-200 mt-4 tracking-tight">
                    {{ $leadCalled?->service->name ?? 'Awaiting Next Patient' }}
                </div>

                <div id="lead-clinician" class="text-xs font-semibold text-slate-400 mt-2">
                    {{ $leadCalled?->servedBy ? 'Attending: Dr. ' . $leadCalled->servedBy->name : 'Please proceed to designated consultation room' }}
                </div>
            </div>
        </div>

        {{-- Right: Department Boards Matrix (7 cols) --}}
        <div class="lg:col-span-7 flex flex-col justify-center">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Department Status Boards</h2>
                <span class="text-xs font-mono text-emerald-400 flex items-center gap-1.5 font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Central Telemetry Active
                </span>
            </div>

            <div id="departments-container" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($services as $svc)
                    @php
                        $active = $svc->activeQueueEntries()->first();
                    @endphp
                    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-slate-700 transition-colors flex items-center justify-between shadow-sm">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] font-mono font-bold uppercase text-indigo-400 bg-indigo-950/80 px-2 py-0.5 rounded border border-indigo-800/50">
                                    {{ $svc->prefix }}
                                </span>
                                <span class="text-xs text-slate-400">Wait: <strong class="text-white">{{ $svc->waitingCount }}</strong></span>
                            </div>
                            <h3 class="text-base font-bold text-white mt-1.5">{{ $svc->name }}</h3>
                            <p class="text-[11px] text-slate-500 mt-0.5">Est. Duration: ~{{ $svc->avg_duration_minutes }}m</p>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Serving</span>
                            <span class="text-2xl sm:text-3xl font-black font-mono-numbers text-emerald-400">
                                {{ $active?->queue_number ?? '—' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>

    {{-- Bottom Scrolling Notice Marquee & Ticker --}}
    <footer class="pt-4 border-t border-slate-800/80 space-y-3">
        {{-- Marquee Alert Banner --}}
        <div class="bg-slate-900/60 border border-slate-800 rounded-xl px-4 py-2 overflow-hidden flex items-center gap-3">
            <span class="flex-shrink-0 text-xs font-black text-amber-400 flex items-center gap-1">
                <span>📢</span> NOTICE:
            </span>
            <div class="overflow-hidden w-full relative">
                <div class="marquee-track text-xs text-slate-300 font-medium">
                    🏥 Welcome to MediQueue Central Hospital. Please have your Medical Record Number (MRN) ready upon room entry &bull; Emergency Code Red trauma cases receive immediate clinical priority &bull; Mobile live tracking is available at mediqueue.test &bull; Clinic Hours: 08:00 AM – 06:00 PM daily.
                </div>
            </div>
        </div>

        {{-- Sub-footer metadata --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] text-slate-500 font-medium">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                <span>MediQueue Display Telemetry &mdash; Auto-synchronized</span>
            </div>
            <div class="flex items-center gap-4">
                <span>High-Definition Display Mode</span>
                <span>&bull;</span>
                <a href="{{ route('home') }}" class="text-indigo-400 hover:underline">Exit to Portal</a>
            </div>
        </div>
    </footer>

    <script>
        let soundEnabled = true;
        let lastLeadTicket = "{{ $leadCalled?->queue_number ?? '' }}";

        function toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => console.log(err));
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        function toggleAudio() {
            soundEnabled = !soundEnabled;
            document.getElementById('soundLabel').textContent = soundEnabled ? 'Chime: ON' : 'Chime: OFF';
            document.getElementById('soundToggleBtn').classList.toggle('text-rose-400', !soundEnabled);
        }

        // Web Audio API Ding-Dong Chime (Synthesized, no external audio files required!)
        function playChime() {
            if (!soundEnabled) return;
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                
                // Tone 1: High
                const osc1 = audioCtx.createOscillator();
                const gain1 = audioCtx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(659.25, audioCtx.currentTime); // E5
                gain1.gain.setValueAtTime(0.3, audioCtx.currentTime);
                gain1.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.6);
                osc1.connect(gain1);
                gain1.connect(audioCtx.destination);
                osc1.start();
                osc1.stop(audioCtx.currentTime + 0.6);

                // Tone 2: Low (Ding-Dong)
                setTimeout(() => {
                    const osc2 = audioCtx.createOscillator();
                    const gain2 = audioCtx.createGain();
                    osc2.type = 'sine';
                    osc2.frequency.setValueAtTime(523.25, audioCtx.currentTime); // C5
                    gain2.gain.setValueAtTime(0.35, audioCtx.currentTime);
                    gain2.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.8);
                    osc2.connect(gain2);
                    gain2.connect(audioCtx.destination);
                    osc2.start();
                    osc2.stop(audioCtx.currentTime + 0.8);
                }, 250);
            } catch (e) {
                console.log("Audio not allowed yet by browser policy:", e);
            }
        }

        // Live Polling for Screen
        const dataUrl = "{{ route('display.data') }}";

        function updateScreen() {
            fetch(dataUrl)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('live-clock').textContent = data.time;
                    document.getElementById('live-date').textContent = data.date;

                    // Lead called
                    if (data.called && data.called.length > 0) {
                        const currentTicket = data.called[0].queue_number;
                        if (currentTicket && currentTicket !== lastLeadTicket && lastLeadTicket !== '') {
                            playChime();
                        }
                        lastLeadTicket = currentTicket;

                        document.getElementById('lead-number').textContent = currentTicket;
                        document.getElementById('lead-department').textContent = data.called[0].service_name;
                    } else {
                        document.getElementById('lead-number').textContent = '—';
                        document.getElementById('lead-department').textContent = 'Awaiting Next Patient';
                    }

                    // Departments grid
                    if (data.departments) {
                        const container = document.getElementById('departments-container');
                        container.innerHTML = data.departments.map(dept => `
                            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-slate-700 transition-colors flex items-center justify-between shadow-sm">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[11px] font-mono font-bold uppercase text-indigo-400 bg-indigo-950/80 px-2 py-0.5 rounded border border-indigo-800/50">
                                            ${dept.prefix}
                                        </span>
                                        <span class="text-xs text-slate-400">Wait: <strong class="text-white">${dept.waitingCount}</strong></span>
                                    </div>
                                    <h3 class="text-base font-bold text-white mt-1.5">${dept.name}</h3>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Est. Duration: ~${dept.avg_duration_minutes ?? 15}m</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] uppercase font-bold text-slate-500 block">Serving</span>
                                    <span class="text-2xl sm:text-3xl font-black font-mono-numbers text-emerald-400">
                                        ${dept.current}
                                    </span>
                                </div>
                            </div>
                        `).join('');
                    }
                })
                .catch(err => console.error("Display poll error:", err));
        }

        setInterval(updateScreen, 3000);
    </script>
</body>
</html>
