<x-layouts.app title="Create Staff Account">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-indigo-600 mb-6 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Users
        </a>

        <div class="card p-8">
            <h1 class="text-2xl font-bold text-slate-900 mb-1">Create Staff / Admin Account</h1>
            <p class="text-slate-500 text-sm mb-6">Create internal clinic staff credentials for managing queues.</p>

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="form-label">Full Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        class="form-input @error('name') border-rose-400 bg-rose-50 @enderror"
                        placeholder="e.g. Dr. Aisyah Rahman"
                    >
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="form-label">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="form-input @error('email') border-rose-400 bg-rose-50 @enderror"
                        placeholder="e.g. dr.aisyah@mediqueue.test"
                    >
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="form-label">Phone Number (Optional)</label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        value="{{ old('phone') }}"
                        class="form-input"
                        placeholder="+60 12 345 6789"
                    >
                </div>

                <div>
                    <label for="role" class="form-label">Assigned Role</label>
                    <select id="role" name="role" required class="form-input @error('role') border-rose-400 bg-rose-50 @enderror">
                        <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Clinical Staff (Doctor / Nurse / Officer)</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>System Administrator</option>
                    </select>
                    @error('role') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="form-label">Initial Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="form-input @error('password') border-rose-400 bg-rose-50 @enderror"
                        placeholder="••••••••"
                    >
                    @error('password') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        class="form-input"
                        placeholder="••••••••"
                    >
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
