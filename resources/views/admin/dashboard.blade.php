@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <h2 class="heading-2">Selamat datang, {{ auth()->user()->name }}</h2>
    <p class="mt-1 text-body-muted">Ringkasan sistem Gunadarma Academic Service Navigator.</p>

    {{-- Stat cards --}}
    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="card p-5">
            <p class="text-sm text-text-muted">Total Layanan</p>
            <p class="mt-1 text-2xl font-semibold text-text">{{ $stats['total_services'] }}</p>
        </div>
        <div class="card p-5">
            <p class="text-sm text-text-muted">Total Kategori</p>
            <p class="mt-1 text-2xl font-semibold text-text">{{ $stats['total_categories'] }}</p>
        </div>
        <div class="card p-5">
            <p class="text-sm text-text-muted">Total Pertanyaan</p>
            <p class="mt-1 text-2xl font-semibold text-text">{{ $stats['total_questions'] }}</p>
        </div>
        <div class="card p-5">
            <p class="text-sm text-text-muted">Confidence Rendah</p>
            <p class="mt-1 text-2xl font-semibold text-text">{{ $stats['low_confidence'] }}</p>
            <p class="mt-0.5 text-xs text-text-muted">di bawah {{ number_format($threshold * 100, 0) }}%</p>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Intent stats --}}
        <div class="card p-5">
            <h3 class="heading-3">Statistik Intent</h3>

            @if ($intentStats->isEmpty())
                <p class="mt-3 text-sm text-text-muted">
                    Belum ada data pertanyaan. Statistik intent akan muncul setelah pencarian mulai digunakan.
                </p>
            @else
                @php $max = $intentStats->max('total'); @endphp
                <div class="mt-4 space-y-3">
                    @foreach ($intentStats as $row)
                        <div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-text">{{ $row->category->name ?? 'Tidak diketahui' }}</span>
                                <span class="text-text-muted">{{ $row->total }}</span>
                            </div>
                            <div class="mt-1 h-2 rounded-full bg-background">
                                <div class="h-2 rounded-full bg-primary" style="width: {{ $max > 0 ? round($row->total / $max * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Recent activity --}}
        <div class="card p-5">
            <h3 class="heading-3">Aktivitas Terbaru</h3>

            @if ($recentActivity->isEmpty())
                <p class="mt-3 text-sm text-text-muted">
                    Belum ada aktivitas pencarian.
                </p>
            @else
                <ul class="mt-4 divide-y divide-border">
                    @foreach ($recentActivity as $entry)
                        <li class="py-3 first:pt-0 last:pb-0">
                            <p class="truncate text-sm text-text">"{{ $entry->question }}"</p>
                            <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-text-muted">
                                <span>{{ $entry->category->name ?? 'Tidak dikenali' }}</span>
                                @if (! is_null($entry->confidence))
                                    <span>&middot;</span>
                                    <span>{{ number_format($entry->confidence * 100, 0) }}%</span>
                                @endif
                                <span>&middot;</span>
                                <span>{{ $entry->created_at->diffForHumans() }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
