@extends('layouts.app')

@section('title', 'Gunadarma Academic Service Navigator — Temukan layanan kampus yang kamu butuhkan')

@section('content')

    {{-- Hero + search --}}
    <section id="cari" class="scroll-mt-24 bg-surface">
        <div class="mx-auto max-w-3xl px-4 py-16 text-center sm:px-6 sm:py-24">
            <h1 class="heading-1">Butuh layanan Gunadarma?</h1>
            <p class="mx-auto mt-4 max-w-xl text-body-muted">
                Tulis apa yang ingin kamu cari. Sistem akan membantu menemukan layanan yang paling sesuai.
            </p>

            <form method="GET" action="{{ url('/') }}#hasil" class="mt-8">
                <div class="flex items-center gap-2 rounded-2xl border border-border bg-surface p-2 shadow-sm focus-within:border-primary focus-within:ring-1 focus-within:ring-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-5 w-5 shrink-0 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
                    </svg>
                    <input
                        id="search-input"
                        type="text"
                        name="q"
                        value="{{ $query }}"
                        placeholder="Contoh: &quot;Saya mau cek jadwal kuliah...&quot;"
                        class="min-w-0 flex-1 border-0 bg-transparent py-2 text-sm text-text placeholder:text-text-muted focus:outline-none focus:ring-0 sm:text-base"
                        autocomplete="off"
                    >
                    <button type="submit" class="btn-primary shrink-0">
                        Cari
                    </button>
                </div>
            </form>

            <div class="mt-5 flex flex-wrap items-center justify-center gap-2">
                @foreach (['Jadwal Kuliah', 'Lihat Nilai', 'KRS', 'Pendaftaran Sidang', 'Pembayaran Kuliah', 'Wisuda'] as $chip)
                    <button
                        type="button"
                        data-quick-search="{{ $chip }}"
                        class="rounded-full border border-border bg-surface px-3.5 py-1.5 text-xs font-medium text-text-muted transition-colors hover:border-primary hover:text-primary sm:text-sm"
                    >
                        {{ $chip }}
                    </button>
                @endforeach
            </div>

            @if ($searched)
                <div id="hasil" class="scroll-mt-24 mx-auto mt-8 max-w-xl text-left">
                    @if ($result)
                        <div class="card p-6">
                            <p class="text-xs font-medium uppercase tracking-wide text-text-muted">Layanan yang kami temukan</p>

                            <div class="mt-2 flex items-start gap-3">
                                <span class="text-2xl" aria-hidden="true">{{ $result->category->icon() }}</span>
                                <div>
                                    <x-badge variant="primary">{{ $result->category->name }}</x-badge>
                                    <h2 class="heading-3 mt-1">{{ $result->name }}</h2>
                                </div>
                            </div>

                            @if ($result->description)
                                <p class="mt-3 text-sm text-text-muted">{{ $result->description }}</p>
                            @endif

                            @if ($source === 'ai' && ! is_null($confidence))
                                <div class="mt-4">
                                    <div class="flex items-center justify-between text-xs text-text-muted">
                                        <span>AI Confidence</span>
                                        <span>{{ number_format($confidence * 100, 0) }}%</span>
                                    </div>
                                    <div class="mt-1 h-2 rounded-full bg-background">
                                        <div class="h-2 rounded-full bg-primary" style="width: {{ round($confidence * 100) }}%"></div>
                                    </div>
                                </div>
                            @elseif ($source === 'keyword')
                                <p class="mt-3 text-xs text-text-muted">
                                    Dicocokkan berdasarkan kata kunci — layanan AI sedang tidak aktif.
                                </p>
                            @endif

                            <x-button href="/layanan/{{ $result->slug }}" variant="primary" class="mt-5 w-full sm:w-auto">
                                Lihat Layanan
                            </x-button>
                        </div>
                    @elseif ($lowConfidence)
                        <x-alert variant="warning">
                            <p class="font-medium">Kami belum yakin dengan layanan yang kamu cari</p>
                            <p class="mt-1 text-warning/90">
                                Coba gunakan pertanyaan yang lebih spesifik, misalnya "Saya ingin melihat jadwal kuliah semester ini".
                            </p>
                            <p class="mt-2 text-xs text-warning/70">
                                AI confidence {{ number_format($confidence * 100, 0) }}% — di bawah batas minimum {{ number_format($threshold * 100, 0) }}%.
                            </p>
                        </x-alert>
                    @else
                        <x-alert variant="warning">
                            <p class="font-medium">Kami belum menemukan layanan yang cocok</p>
                            <p class="mt-1 text-warning/90">
                                Coba gunakan pertanyaan yang lebih spesifik, misalnya "Saya ingin melihat jadwal kuliah semester ini", atau jelajahi seluruh layanan di halaman <a href="/layanan" class="underline underline-offset-2">Layanan</a>.
                            </p>
                        </x-alert>
                    @endif
                </div>
            @endif
        </div>
    </section>

    {{-- Category preview --}}
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <div class="mb-8 flex items-end justify-between">
            <div>
                <h2 class="heading-2">Jelajahi kategori layanan</h2>
                <p class="mt-1 text-body-muted">Sebagian layanan akademik yang tersedia melalui navigator ini.</p>
            </div>
            <a href="/layanan" class="hidden shrink-0 text-sm font-medium text-primary hover:text-primary-hover sm:block">
                Lihat semua &rarr;
            </a>
        </div>

        @if ($categories->isEmpty())
            <x-alert variant="warning">
                Belum ada kategori layanan. Tambahkan kategori dari panel admin untuk mulai menggunakan sistem.
            </x-alert>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($categories as $category)
                    <a href="/layanan" class="card-hover block p-5">
                        <h3 class="heading-3">{{ $category->name }}</h3>
                        <p class="mt-1.5 text-sm text-text-muted">
                            {{ $category->description ?? 'Layanan terkait '.$category->name }}
                        </p>
                    </a>
                @endforeach
            </div>
        @endif

        <a href="/layanan" class="mt-6 block text-center text-sm font-medium text-primary hover:text-primary-hover sm:hidden">
            Lihat semua layanan &rarr;
        </a>
    </section>

@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-quick-search]').forEach((chip) => {
            chip.addEventListener('click', () => {
                const input = document.getElementById('search-input');
                input.value = chip.dataset.quickSearch;
                input.focus();
            });
        });
    </script>
@endpush
