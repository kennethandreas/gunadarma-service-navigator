@extends('layouts.app')

@section('title', 'Layanan — Gunadarma Academic Service Navigator')

@section('content')

    <section class="bg-surface">
        <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
            <h1 class="heading-1">Semua Layanan</h1>
            <p class="mt-2 max-w-xl text-body-muted">
                Jelajahi seluruh layanan akademik Gunadarma yang tersedia melalui navigator ini.
            </p>

            <form method="GET" action="/layanan" class="mt-6 max-w-md">
                <label for="q" class="sr-only">Cari layanan</label>
                <div class="flex gap-2">
                    <input
                        id="q"
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Cari nama layanan..."
                        class="form-input"
                    >
                    <button type="submit" class="btn-secondary shrink-0">Cari</button>
                </div>
            </form>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
        @if ($categories->isEmpty())
            @if ($search !== '')
                <x-alert variant="warning">
                    <p class="font-medium">Tidak ada layanan yang cocok dengan "{{ $search }}"</p>
                    <p class="mt-1 text-warning/90">Coba kata kunci lain, atau <a href="/layanan" class="underline underline-offset-2">lihat semua layanan</a>.</p>
                </x-alert>
            @else
                <x-alert variant="warning">
                    <p class="font-medium">Belum ada layanan</p>
                    <p class="mt-1 text-warning/90">Layanan akan muncul di sini setelah ditambahkan dari panel admin.</p>
                </x-alert>
            @endif
        @else
            <div class="space-y-12">
                @foreach ($categories as $category)
                    <div>
                        <div class="mb-4 flex items-center gap-2">
                            <span class="text-xl" aria-hidden="true">{{ $category->icon() }}</span>
                            <h2 class="heading-3">{{ $category->name }}</h2>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($category->services as $service)
                                <x-service-card :service="$service" :icon="$category->icon()" />
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

@endsection
