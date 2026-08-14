<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($title) ? $title . ' — MediQueue' : 'Sign In — MediQueue' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,100..900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans antialiased">
    <div class="min-h-screen flex">
        {{-- Left Panel: Brand --}}
        <div class="hidden lg:flex lg:w-1/2 xl:w-[45%] bg-gradient-to-br from-indigo-900 via-indigo-800 to-indigo-700 relative overflow-hidden flex-col justify-between p-12">
            {{-- Pattern background --}}
            <div class="absolute inset-0 opacity-10">
                <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                            <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#grid)" />
                </svg>
            </div>

            {{-- Logo --}}
            <div class="relative">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm border border-white/30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-white tracking-tight">MediQueue</span>
                </a>
            </div>

            {{-- Content --}}
            <div class="relative">
                <blockquote class="text-white">
                    <p class="text-3xl font-semibold leading-snug mb-4">
                        "Effortless clinic queues for patients and staff."
                    </p>
                    <p class="text-indigo-200 text-lg leading-relaxed">
                        Join a queue in seconds. Track your position in real time. Let clinic staff focus on care, not coordination.
                    </p>
                </blockquote>

                <div class="mt-10 grid grid-cols-3 gap-4">
                    @foreach([['5+', 'Clinic services'], ['Real-time', 'Queue tracking'], ['Zero', 'Physical queuing']] as $stat)
                        <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl p-4 text-center">
                            <p class="text-2xl font-bold text-white">{{ $stat[0] }}</p>
                            <p class="text-xs text-indigo-200 mt-1">{{ $stat[1] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Footer text --}}
            <div class="relative">
                <p class="text-indigo-300 text-sm">Smart Clinic Queue Management &copy; {{ date('Y') }}</p>
            </div>
        </div>

        {{-- Right Panel: Form --}}
        <div class="flex-1 flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-md">
                {{-- Mobile logo --}}
                <div class="lg:hidden mb-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                        <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-slate-900">MediQueue</span>
                    </a>
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
