---
name: blade-ui
description: UI component construction, Tailwind CSS styling patterns, responsive tables, and accessibility standards for MediQueue.
---

# Blade UI & Component Skill Guide

Use this skill when constructing or styling Blade templates, layout structures, status badges, forms, and live queue displays for **MediQueue**.

---

## 1. Dynamic Status Badge Component Example

```blade
{{-- resources/views/components/badge.blade.php --}}
@props(['status' => 'WAITING'])

@php
    $classes = match (strtoupper($status)) {
        'WAITING'   => 'bg-amber-50 text-amber-700 border-amber-200',
        'CALLED'    => 'bg-indigo-50 text-indigo-700 border-indigo-200 animate-pulse font-bold',
        'SERVING'   => 'bg-emerald-50 text-emerald-700 border-emerald-200 font-semibold',
        'COMPLETED' => 'bg-slate-100 text-slate-600 border-slate-200',
        'CANCELLED', 'NO-SHOW' => 'bg-rose-50 text-rose-700 border-rose-200',
        'EMERGENCY' => 'bg-red-600 text-white border-red-700 font-extrabold',
        default     => 'bg-slate-100 text-slate-700 border-slate-200',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-1 rounded-full text-xs border ' . $classes]) }}>
    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ strtoupper($status) === 'CALLED' ? 'bg-indigo-500 animate-ping' : 'bg-current' }}"></span>
    {{ strtoupper($status) }}
</span>
```

---

## 2. Kiosk Patient Registration View Template

```blade
{{-- resources/views/kiosk/index.blade.php --}}
<x-layout title="MediQueue - Kiosk Check-In">
    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight sm:text-4xl">
                Welcome to City Clinic
            </h1>
            <p class="mt-2 text-lg text-slate-600">
                Select a medical service below to receive your digital queue ticket.
            </p>
        </div>

        <form action="{{ route('kiosk.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Service Selection Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($services as $service)
                    <label class="relative block rounded-xl border border-slate-200 p-6 bg-white shadow-sm hover:border-indigo-500 hover:ring-2 hover:ring-indigo-500 cursor-pointer transition">
                        <input type="radio" name="service_id" value="{{ $service->id }}" class="sr-only" required>
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-lg text-sm font-bold bg-indigo-50 text-indigo-700 mb-2">
                                    {{ $service->code }}
                                </span>
                                <h3 class="text-xl font-bold text-slate-900">{{ $service->name }}</h3>
                                <p class="text-sm text-slate-500 mt-1">{{ $service->description }}</p>
                            </div>
                            <span class="text-xs font-semibold text-slate-400">
                                ~{{ $service->avg_duration_minutes }} mins/pt
                            </span>
                        </div>
                    </label>
                @endforeach
            </div>

            <!-- Patient Information Card -->
            <x-card class="space-y-4">
                <h3 class="text-lg font-bold text-slate-900">Patient Details</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700">Full Name</label>
                        <input type="text" name="name" id="name" required class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700">Mobile Phone Number</label>
                        <input type="tel" name="phone" id="phone" required class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>
            </x-card>

            <x-button type="submit" class="w-full py-4 text-lg font-bold text-center">
                Get Queue Ticket
            </x-button>
        </form>
    </div>
</x-layout>
```
