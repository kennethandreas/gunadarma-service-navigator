@extends('layouts.admin')

@section('title', 'Dataset')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="heading-2">Dataset</h2>
            <p class="mt-1 text-body-muted">Kelola contoh pertanyaan yang dipakai untuk melatih model klasifikasi intent.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.dataset.export') }}" class="btn-secondary">⬇️ Export CSV</a>
            <x-button href="{{ route('admin.dataset.create') }}" variant="primary">
                + Tambah Contoh
            </x-button>
        </div>
    </div>

    <x-alert variant="warning" class="mt-6">
        <p class="font-medium">Perubahan dataset tidak otomatis diterapkan</p>
        <p class="mt-1 text-warning/90">
            Setelah menambah, mengedit, atau menghapus contoh pertanyaan di sini, buka halaman
            <a href="{{ route('admin.model.index') }}" class="underline font-medium">Model AI</a>
            dan klik "Train / Retrain Model" agar model AI belajar dari data terbaru.
        </p>
    </x-alert>

    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="card p-5">
            <p class="text-sm text-text-muted">Total Contoh</p>
            <p class="mt-1 text-2xl font-semibold text-text">{{ $total }}</p>
        </div>
        @foreach ($perIntent->take(3) as $intent => $count)
            <div class="card p-5">
                <p class="text-sm text-text-muted">{{ $categories->firstWhere('slug', $intent)->name ?? $intent }}</p>
                <p class="mt-1 text-2xl font-semibold text-text">{{ $count }}</p>
            </div>
        @endforeach
    </div>

    @if ($perIntent->isNotEmpty())
        <div class="card mt-4 overflow-hidden">
            <div class="border-b border-border px-5 py-4">
                <h3 class="heading-3">Distribusi per Intent</h3>
            </div>
            <div class="flex flex-wrap gap-x-8 gap-y-3 p-5">
                @foreach ($perIntent as $intent => $count)
                    <div class="flex items-center gap-2 text-sm">
                        <span class="font-medium text-text">{{ $categories->firstWhere('slug', $intent)->name ?? $intent }}</span>
                        <span class="text-text-muted">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <form method="GET" action="{{ route('admin.dataset.index') }}" class="mt-6 flex flex-wrap gap-3">
        <input
            type="text"
            name="q"
            value="{{ $search }}"
            placeholder="Cari teks pertanyaan..."
            class="form-input max-w-xs"
        >
        <select name="intent" class="form-input max-w-[14rem]" onchange="this.form.submit()">
            <option value="" @selected($intentFilter === '')>Semua Intent</option>
            @foreach ($categories as $category)
                <option value="{{ $category->slug }}" @selected($intentFilter === $category->slug)>{{ $category->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-secondary">Filter</button>
        @if ($search !== '' || $intentFilter !== '')
            <a href="{{ route('admin.dataset.index') }}" class="btn-ghost">Reset</a>
        @endif
    </form>

    <div class="card mt-6 overflow-hidden">
        @if ($rows->isEmpty())
            <div class="p-10 text-center">
                <p class="font-medium text-text">
                    {{ $search !== '' || $intentFilter !== '' ? 'Tidak ada contoh yang cocok' : 'Dataset masih kosong' }}
                </p>
                <p class="mt-1 text-sm text-text-muted">
                    {{ $search !== '' || $intentFilter !== '' ? 'Coba ubah kata kunci atau filter.' : 'Tambahkan contoh pertanyaan untuk mulai melatih model.' }}
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-border bg-background text-xs uppercase tracking-wide text-text-muted">
                        <tr>
                            <th class="px-5 py-3 font-medium">Pertanyaan</th>
                            <th class="px-5 py-3 font-medium">Intent</th>
                            <th class="px-5 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($rows as $row)
                            <tr>
                                <td class="px-5 py-3 text-text">
                                    <span class="block max-w-md truncate" title="{{ $row['text'] }}">{{ $row['text'] }}</span>
                                </td>
                                <td class="px-5 py-3 text-text-muted">
                                    <x-badge variant="neutral">{{ $categories->firstWhere('slug', $row['intent'])->name ?? $row['intent'] }}</x-badge>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('admin.dataset.edit', $row['id']) }}" class="text-sm font-medium text-primary hover:text-primary-hover">Edit</a>
                                        <button
                                            type="button"
                                            onclick="openDeleteModal('{{ route('admin.dataset.destroy', $row['id']) }}', 'contoh pertanyaan ini')"
                                            class="text-sm font-medium text-error hover:text-error/80"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-border px-5 py-4">
                {{ $rows->links() }}
            </div>
        @endif
    </div>
@endsection
