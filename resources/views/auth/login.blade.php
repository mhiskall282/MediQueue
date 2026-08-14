<x-layouts.auth title="Sign In">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 mb-1">Welcome back</h1>
        <p class="text-slate-500 text-sm mb-8">Sign in to access your MediQueue account.</p>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="form-label">Email address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    autofocus
                    class="form-input @error('email') border-rose-400 bg-rose-50 @enderror"
                    placeholder="you@example.com"
                    aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                    aria-describedby="{{ $errors->has('email') ? 'email-error' : '' }}"
                >
                @error('email')
                    <p id="email-error" class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="form-input @error('password') border-rose-400 bg-rose-50 @enderror"
                    placeholder="••••••••"
                    aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                >
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember --}}
            <div class="flex items-center">
                <input type="checkbox" id="remember" name="remember" class="w-4 h-4 text-indigo-600 border-slate-300 rounded">
                <label for="remember" class="ml-2 text-sm text-slate-600">Keep me signed in</label>
            </div>

            {{-- Submit --}}
            <button type="submit" id="login-submit" class="btn btn-primary w-full py-2.5 text-sm justify-center">
                Sign in to MediQueue
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-indigo-600 font-medium hover:text-indigo-700">Create one free</a>
        </p>

        {{-- Demo credentials hint --}}
        <div class="mt-6 p-4 bg-slate-50 border border-slate-200 rounded-lg">
            <p class="text-xs font-semibold text-slate-600 mb-2">Demo Credentials</p>
            <div class="space-y-1 text-xs text-slate-500">
                <p><span class="font-medium text-slate-700">Admin:</span> admin@mediqueue.test / password</p>
                <p><span class="font-medium text-slate-700">Staff:</span> dr.sarah@mediqueue.test / password</p>
                <p><span class="font-medium text-slate-700">Patient:</span> john.doe@example.com / password</p>
            </div>
        </div>
    </div>
</x-layouts.auth>
