<x-layouts.auth title="Mandatory Password Update">
    <div class="space-y-6">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300 mb-3">
                <span>🛡️</span> Mandatory First-Time Login Security
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Establish Your Private Password</h1>
            <p class="text-xs text-slate-600 mt-1">
                To comply with Hospital Governance, HIPAA, and ISO-27001 data protection protocols, you must replace your temporary administrator-provisioned credentials with your own private secure password.
            </p>
        </div>

        <form method="POST" action="{{ route('password.force-change.update') }}" class="space-y-4">
            @csrf

            {{-- New Password --}}
            <div>
                <label for="password" class="form-label text-xs">New Secure Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    class="form-input text-xs @error('password') border-rose-400 bg-rose-50 @enderror"
                    placeholder="At least 8 characters"
                >
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="password_confirmation" class="form-label text-xs">Confirm New Password</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="form-input text-xs"
                    placeholder="Repeat your password"
                >
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-[11px] text-slate-600 space-y-1">
                <span class="font-bold text-slate-800 block">Password Guidelines:</span>
                <div>• Minimum 8 characters</div>
                <div>• Mix of uppercase letters, numbers, and symbols recommended</div>
                <div>• Do not share your clinical credentials with any third party</div>
            </div>

            <button type="submit" class="btn btn-primary w-full justify-center text-xs font-bold py-3 shadow-md">
                🔒 Save Password & Access Portal
            </button>
        </form>

        <div class="text-center pt-2">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs text-slate-500 hover:text-rose-600 hover:underline">
                    Sign Out & Return Later
                </button>
            </form>
        </div>
    </div>
</x-layouts.auth>
