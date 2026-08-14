<x-layouts.app title="System & Clinic Settings">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 mb-2">
                Administration
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">System & Clinic Settings</h1>
            <p class="text-slate-500 text-sm mt-1">Configure healthcare facility identity, operational policies, and notification rules.</p>
        </div>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-8">
            @csrf
            @method('PUT')

            @foreach($settings as $group => $items)
                <div class="card p-6 sm:p-8">
                    <h2 class="text-lg font-bold text-slate-900 uppercase tracking-wider text-xs text-indigo-600 mb-6 pb-3 border-b border-slate-100">
                        {{ ucfirst($group) }} Configuration
                    </h2>

                    <div class="space-y-6">
                        @foreach($items as $s)
                            <div>
                                <label for="{{ $s->key }}" class="form-label font-bold text-slate-800 text-sm">
                                    {{ $s->label }}
                                </label>
                                @if($s->description)
                                    <p class="text-xs text-slate-500 mb-2">{{ $s->description }}</p>
                                @endif

                                @if($s->type === 'boolean')
                                    <select id="{{ $s->key }}" name="{{ $s->key }}" class="form-input">
                                        <option value="1" {{ $s->value == '1' ? 'selected' : '' }}>Enabled</option>
                                        <option value="0" {{ $s->value == '0' ? 'selected' : '' }}>Disabled</option>
                                    </select>
                                @elseif($s->type === 'textarea')
                                    <textarea id="{{ $s->key }}" name="{{ $s->key }}" rows="3" class="form-input">{{ old($s->key, $s->value) }}</textarea>
                                @else
                                    <input
                                        type="{{ $s->type }}"
                                        id="{{ $s->key }}"
                                        name="{{ $s->key }}"
                                        value="{{ old($s->key, $s->value) }}"
                                        class="form-input"
                                    >
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary shadow-md">
                    Save System Settings
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
