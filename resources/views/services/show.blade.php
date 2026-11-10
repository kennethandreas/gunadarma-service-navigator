@extends('layouts.app')

@section('title', $service->name.' — Gunadarma Academic Service Navigator')

@section('content')

    <section class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
        <a href="/layanan" class="inline-flex items-center gap-1.5 text-sm font-medium text-text-muted hover:text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
            </svg>
            Semua Layanan
        </a>

        <div class="mt-6 flex items-start gap-4">
            <span class="text-3xl" aria-hidden="true">{{ $service->category->icon() }}</span>
            <div>
                <x-badge variant="primary">{{ $service->category->name }}</x-badge>
                <h1 class="heading-1 mt-2">{{ $service->name }}</h1>
            </div>
        </div>

        @if ($service->description)
            <p class="mt-4 text-body-muted">{{ $service->description }}</p>
        @endif

        @if (! empty($service->capabilities))
            <div class="card mt-8 p-6">
                <h2 class="heading-3">Apa yang bisa kamu lakukan?</h2>
                <ul class="mt-4 space-y-2.5">
                    @foreach ($service->capabilities as $capability)
                        <li class="flex items-start gap-2.5 text-sm text-text">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 shrink-0 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ $capability }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mt-8">
            @if ($service->url)
                <x-button href="{{ $service->url }}" variant="primary" class="w-full sm:w-auto" target="_blank" rel="noopener noreferrer">
                    Buka Website Resmi
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </x-button>
                <p class="mt-2 text-xs text-text-muted">Kamu akan diarahkan ke situs resmi Universitas Gunadarma.</p>
            @else
                <x-alert variant="warning">
                    Link resmi untuk layanan ini belum tersedia. Silakan hubungi admin.
                </x-alert>
            @endif
        </div>
    </section>

@endsection
