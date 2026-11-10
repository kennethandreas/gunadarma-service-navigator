@extends('layouts.admin')

@section('title', 'Kategori')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="heading-2">Kategori</h2>
            <p class="mt-1 text-body-muted">Kelola kategori/intent layanan.</p>
        </div>
        <x-button href="{{ route('admin.categories.create') }}" variant="primary">
            + Tambah Kategori
        </x-button>
    </div>

    <form method="GET" action="{{ route('admin.categories.index') }}" class="mt-6 flex flex-wrap gap-3">
        <input
            type="text"
            name="q"
            value="{{ $search }}"
            placeholder="Cari nama kategori..."
            class="form-input max-w-xs"
        >
        <select name="status" class="form-input max-w-[10rem]" onchange="this.form.submit()">
            <option value="" @selected($status === '')>Semua Status</option>
            <option value="active" @selected($status === 'active')>Aktif</option>
            <option value="inactive" @selected($status === 'inactive')>Nonaktif</option>
        </select>
        <button type="submit" class="btn-secondary">Filter</button>
        @if ($search !== '' || $status !== '')
            <a href="{{ route('admin.categories.index') }}" class="btn-ghost">Reset</a>
        @endif
    </form>

    <div class="card mt-6 overflow-hidden">
        @if ($categories->isEmpty())
            <div class="p-10 text-center">
                <p class="font-medium text-text">
                    {{ $search !== '' || $status !== '' ? 'Tidak ada kategori yang cocok' : 'Belum ada kategori' }}
                </p>
                <p class="mt-1 text-sm text-text-muted">
                    {{ $search !== '' || $status !== '' ? 'Coba ubah kata kunci atau filter.' : 'Tambahkan kategori untuk mulai menggunakan sistem.' }}
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-border bg-background text-xs uppercase tracking-wide text-text-muted">
                        <tr>
                            <th class="px-5 py-3 font-medium">Nama</th>
                            <th class="px-5 py-3 font-medium">Slug</th>
                            <th class="px-5 py-3 font-medium">Jumlah Layanan</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($categories as $category)
                            <tr>
                                <td class="px-5 py-3 font-medium text-text">{{ $category->name }}</td>
                                <td class="px-5 py-3 text-text-muted">{{ $category->slug }}</td>
                                <td class="px-5 py-3 text-text-muted">{{ $category->services_count }}</td>
                                <td class="px-5 py-3">
                                    <x-badge :variant="$category->status ? 'success' : 'neutral'">
                                        {{ $category->status ? 'Aktif' : 'Nonaktif' }}
                                    </x-badge>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-sm font-medium text-primary hover:text-primary-hover">Edit</a>
                                        <button
                                            type="button"
                                            onclick="openDeleteModal('{{ route('admin.categories.destroy', $category) }}', '{{ addslashes($category->name) }}{{ $category->services_count > 0 ? ' (beserta '.$category->services_count.' layanan di dalamnya)' : '' }}')"
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
                {{ $categories->links() }}
            </div>
        @endif
    </div>
@endsection
