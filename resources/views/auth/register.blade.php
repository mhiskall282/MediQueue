<x-layouts.auth title="Create Account">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 mb-1">Create your account</h1>
        <p class="text-slate-500 text-sm mb-8">Join MediQueue and skip the physical queue.</p>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            {{-- Name --}}
            <div>
                <label for="name" class="form-label">Full name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autocomplete="name"
                    autofocus
                    class="form-input @error('name') border-rose-400 bg-rose-50 @enderror"
                    placeholder="Your full name"
                >
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

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
                    class="form-input @error('email') border-rose-400 bg-rose-50 @enderror"
                    placeholder="you@example.com"
                >
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Phone (optional) --}}
            <div>
                <label for="phone" class="form-label">
                    Phone number <span class="text-slate-400 font-normal">(optional)</span>
                </label>
                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    value="{{ old('phone') }}"
                    class="form-input"
                    placeholder="+60 12 345 6789"
                >
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    class="form-input @error('password') border-rose-400 bg-rose-50 @enderror"
                    placeholder="At least 8 characters"
                >
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password Confirm --}}
            <div>
                <label for="password_confirmation" class="form-label">Confirm password</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="form-input"
                    placeholder="Repeat your password"
                >
            </div>

            <button type="submit" id="register-submit" class="btn btn-primary w-full py-2.5 text-sm justify-center">
                Create my account
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
            Already have an account?
            <a href="{{ route('login') }}" class="text-indigo-600 font-medium hover:text-indigo-700">Sign in</a>
        </p>
    </div>
</x-layouts.auth>
