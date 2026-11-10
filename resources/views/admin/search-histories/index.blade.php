@extends('layouts.admin')

@section('title', 'Riwayat Pertanyaan')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="heading-2">Riwayat Pertanyaan</h2>
            <p class="mt-1 text-body-muted">Setiap pencarian publik tercatat di sini — tanpa identitas pengguna.</p>
        </div>
        <a
            href="{{ route('admin.search-histories.export', $lowConfidenceOnly ? ['low_confidence' => 1] : []) }}"
            class="btn-secondary"
        >
            ⬇️ Export CSV
        </a>
    </div>

    <form method="GET" action="{{ route('admin.search-histories.index') }}" class="mt-6">
        <label class="flex w-fit items-center gap-2 text-sm text-text">
            <input
                type="checkbox"
                name="low_confidence"
                value="1"
                onchange="this.form.submit()"
                class="rounded border-border text-primary focus:ring-primary"
                @checked($lowConfidenceOnly)
            >
            Tampilkan confidence rendah saja (di bawah {{ number_format($threshold * 100, 0) }}%)
        </label>
    </form>

    <div class="card mt-6 overflow-hidden">
        @if ($histories->isEmpty())
            <div class="p-10 text-center">
                <p class="font-medium text-text">
                    {{ $lowConfidenceOnly ? 'Tidak ada riwayat dengan confidence rendah' : 'Belum ada riwayat pertanyaan' }}
                </p>
                <p class="mt-1 text-sm text-text-muted">
                    {{ $lowConfidenceOnly ? 'Coba lihat semua riwayat.' : 'Riwayat akan muncul setelah pengguna melakukan pencarian di halaman utama.' }}
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-border bg-background text-xs uppercase tracking-wide text-text-muted">
                        <tr>
                            <th class="px-5 py-3 font-medium">Pertanyaan</th>
                            <th class="px-5 py-3 font-medium">Intent</th>
                            <th class="px-5 py-3 font-medium">Confidence</th>
                            <th class="px-5 py-3 font-medium">Layanan</th>
                            <th class="px-5 py-3 font-medium">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($histories as $history)
                            <tr>
                                <td class="px-5 py-3 text-text">
                                    <span class="block max-w-xs truncate" title="{{ $history->question }}">"{{ $history->question }}"</span>
                                </td>
                                <td class="px-5 py-3 text-text-muted">{{ $history->category->name ?? 'Tidak dikenali' }}</td>
                                <td class="px-5 py-3">
                                    @if (is_null($history->confidence))
                                        <span class="text-text-muted">—</span>
                                    @else
                                        <x-badge :variant="$history->confidence < $threshold ? 'warning' : 'success'">
                                            {{ number_format($history->confidence * 100, 0) }}%
                                        </x-badge>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-text-muted">{{ $history->service->name ?? '—' }}</td>
                                <td class="px-5 py-3 text-text-muted">{{ $history->created_at->format('d M Y, H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-border px-5 py-4">
                {{ $histories->links() }}
            </div>
        @endif
    </div>
@endsection
