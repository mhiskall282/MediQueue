<x-layouts.app title="Edit Clinic Service - {{ $service->name }}">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <a href="{{ route('admin.services.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-indigo-600 mb-6 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Services
        </a>

        <div class="card p-8">
            <h1 class="text-2xl font-bold text-slate-900 mb-1">Edit Service: {{ $service->name }}</h1>
            <p class="text-slate-500 text-sm mb-6">Modify department details and estimated service duration.</p>

            <form method="POST" action="{{ route('admin.services.update', $service) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="form-label">Service Department Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $service->name) }}"
                        required
                        class="form-input @error('name') border-rose-400 bg-rose-50 @enderror"
                    >
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="prefix" class="form-label">Queue Prefix</label>
                    <input
                        type="text"
                        id="prefix"
                        name="prefix"
                        value="{{ old('prefix', $service->prefix) }}"
                        required
                        maxlength="10"
                        class="form-input font-mono uppercase @error('prefix') border-rose-400 bg-rose-50 @enderror"
                    >
                    @error('prefix') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="avg_duration_minutes" class="form-label">Average Consultation Duration (Minutes)</label>
                    <input
                        type="number"
                        id="avg_duration_minutes"
                        name="avg_duration_minutes"
                        value="{{ old('avg_duration_minutes', $service->avg_duration_minutes) }}"
                        required
                        min="1"
                        max="120"
                        class="form-input @error('avg_duration_minutes') border-rose-400 bg-rose-50 @enderror"
                    >
                    @error('avg_duration_minutes') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="form-label">Description (Optional)</label>
                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        class="form-input @error('description') border-rose-400 bg-rose-50 @enderror"
                    >{{ old('description', $service->description) }}</textarea>
                    @error('description') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
