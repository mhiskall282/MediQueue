<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
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
        @keyframes flashCall {
            0%, 100% { background-color: rgb(30 27 75); border-color: rgb(99 102 241); }
            50% { background-color: rgb(49 46 129); border-color: rgb(129 140 248); box-shadow: 0 0 40px rgba(99, 102, 241, 0.4); }
        }
        .calling-flash { animation: flashCall 2s infinite ease-in-out; }
    </style>
</head>
<body class="h-full flex flex-col justify-between p-6 sm:p-8 bg-slate-950 text-white overflow-x-hidden selection:bg-indigo-600">

    {{-- Top Header Board --}}
    <header class="flex items-center justify-between pb-6 border-b border-slate-800">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-900/50">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">{{ $clinicName }}</h1>
                <p class="text-xs sm:text-sm font-semibold uppercase tracking-widest text-indigo-400">Live Outpatient Queue Display</p>
            </div>
        </div>

        <div class="flex items-center gap-6 text-right">
            <div>
                <div id="live-clock" class="text-2xl sm:text-3xl font-black font-mono-numbers tracking-tight text-emerald-400">
                    {{ now()->format('g:i:s A') }}
                </div>
                <div id="live-date" class="text-xs text-slate-400 font-medium">
                    {{ now()->format('l, F j, Y') }}
                </div>
            </div>

            <button onclick="toggleFullScreen()" class="p-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-white transition-colors" title="Toggle Fullscreen">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                </svg>
            </button>
        </div>
    </header>

    {{-- Main Screen Grid --}}
    <main class="grid grid-cols-1 lg:grid-cols-12 gap-8 my-auto py-8">
        {{-- Left: Big "NOW CALLING" Featured Box (5 cols) --}}
        <div class="lg:col-span-5 flex flex-col justify-center">
            <div class="calling-flash border-2 border-indigo-500 rounded-3xl p-8 sm:p-10 text-center shadow-2xl relative overflow-hidden">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 mb-6">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-400 animate-ping"></span>
                    Now Calling
                </div>

                <div id="lead-number" class="text-7xl sm:text-8xl md:text-9xl font-black font-mono-numbers tracking-tighter text-white drop-shadow-md">
                    {{ $leadCalled?->queue_number ?? '—' }}
                </div>

                <div id="lead-department" class="text-xl sm:text-2xl font-bold text-indigo-200 mt-4 tracking-tight">
                    {{ $leadCalled?->service->name ?? 'Awaiting Next Patient' }}
                </div>

                <p class="text-xs sm:text-sm text-slate-400 mt-2 font-medium">
                    Please proceed to the designated consultation room.
                </p>
            </div>
        </div>

        {{-- Right: Department Boards Matrix (7 cols) --}}
        <div class="lg:col-span-7 flex flex-col justify-center">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400">Department Status Board</h2>
                <span class="text-xs font-mono text-emerald-400 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Live Syncing
                </span>
            </div>

            <div id="departments-container" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($services as $svc)
                    @php
                        $active = $svc->activeQueueEntries()->first();
                    @endphp
                    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 hover:border-slate-700 transition-colors flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-mono font-bold uppercase text-indigo-400 bg-indigo-950/60 px-2 py-0.5 rounded border border-indigo-800/40">
                                {{ $svc->prefix }}
                            </span>
                            <h3 class="text-base font-bold text-white mt-1.5">{{ $svc->name }}</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Waiting: <strong class="text-slate-200">{{ $svc->waitingCount }}</strong></p>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Serving</span>
                            <span class="text-2xl font-black font-mono-numbers text-emerald-400">
                                {{ $active?->queue_number ?? '—' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>

    {{-- Bottom Scrolling Ticker Bar --}}
    <footer class="pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
            <span>MediQueue Digital Outpatient Display &mdash; Auto Refreshing every 3s</span>
        </div>
        <div class="flex items-center gap-4 text-slate-500 font-medium">
            <span>Clinic Hours: 08:00 AM - 06:00 PM</span>
            <span>&bull;</span>
            <a href="{{ route('home') }}" class="text-indigo-400 hover:underline">Return to Home</a>
        </div>
    </footer>

    <script>
        function toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => console.log(err));
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
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
                        document.getElementById('lead-number').textContent = data.called[0].queue_number;
                        document.getElementById('lead-department').textContent = data.called[0].service_name;
                    } else {
                        document.getElementById('lead-number').textContent = '—';
                        document.getElementById('lead-department').textContent = 'Awaiting Next Patient';
                    }

                    // Departments grid
                    if (data.departments) {
                        const container = document.getElementById('departments-container');
                        container.innerHTML = data.departments.map(dept => `
                            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 hover:border-slate-700 transition-colors flex items-center justify-between">
                                <div>
                                    <span class="text-[11px] font-mono font-bold uppercase text-indigo-400 bg-indigo-950/60 px-2 py-0.5 rounded border border-indigo-800/40">
                                        ${dept.prefix}
                                    </span>
                                    <h3 class="text-base font-bold text-white mt-1.5">${dept.name}</h3>
                                    <p class="text-xs text-slate-400 mt-0.5">Waiting: <strong class="text-slate-200">${dept.waitingCount}</strong></p>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] uppercase font-bold text-slate-500 block">Serving</span>
                                    <span class="text-2xl font-black font-mono-numbers text-emerald-400">
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
