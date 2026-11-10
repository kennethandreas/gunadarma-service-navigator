@extends('layouts.admin')

@section('title', 'Layanan')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="heading-2">Layanan</h2>
            <p class="mt-1 text-body-muted">Kelola layanan dan link resmi Gunadarma.</p>
        </div>
        <x-button href="{{ route('admin.services.create') }}" variant="primary">
            + Tambah Layanan
        </x-button>
    </div>

    <form method="GET" action="{{ route('admin.services.index') }}" class="mt-6 flex flex-wrap gap-3">
        <input
            type="text"
            name="q"
            value="{{ $search }}"
            placeholder="Cari nama layanan..."
            class="form-input max-w-xs"
        >
        <select name="category" class="form-input max-w-[12rem]" onchange="this.form.submit()">
            <option value="" @selected($categoryId === '')>Semua Kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) $categoryId === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <select name="status" class="form-input max-w-[10rem]" onchange="this.form.submit()">
            <option value="" @selected($status === '')>Semua Status</option>
            <option value="active" @selected($status === 'active')>Aktif</option>
            <option value="inactive" @selected($status === 'inactive')>Nonaktif</option>
        </select>
        <button type="submit" class="btn-secondary">Filter</button>
        @if ($search !== '' || $status !== '' || $categoryId !== '')
            <a href="{{ route('admin.services.index') }}" class="btn-ghost">Reset</a>
        @endif
    </form>

    <div class="card mt-6 overflow-hidden">
        @if ($services->isEmpty())
            <div class="p-10 text-center">
                <p class="font-medium text-text">
                    {{ $search !== '' || $status !== '' || $categoryId !== '' ? 'Tidak ada layanan yang cocok' : 'Belum ada layanan' }}
                </p>
                <p class="mt-1 text-sm text-text-muted">
                    {{ $search !== '' || $status !== '' || $categoryId !== '' ? 'Coba ubah kata kunci atau filter.' : 'Tambahkan layanan untuk mulai menggunakan sistem.' }}
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-border bg-background text-xs uppercase tracking-wide text-text-muted">
                        <tr>
                            <th class="px-5 py-3 font-medium">Nama</th>
                            <th class="px-5 py-3 font-medium">Kategori</th>
                            <th class="px-5 py-3 font-medium">URL</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($services as $service)
                            <tr>
                                <td class="px-5 py-3 font-medium text-text">{{ $service->name }}</td>
                                <td class="px-5 py-3 text-text-muted">{{ $service->category->name ?? '—' }}</td>
                                <td class="px-5 py-3 text-text-muted">
                                    <span class="block max-w-[16rem] truncate">{{ $service->url ?: '—' }}</span>
                                </td>
                                <td class="px-5 py-3">
                                    <x-badge :variant="$service->status ? 'success' : 'neutral'">
                                        {{ $service->status ? 'Aktif' : 'Nonaktif' }}
                                    </x-badge>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('admin.services.edit', $service) }}" class="text-sm font-medium text-primary hover:text-primary-hover">Edit</a>
                                        <button
                                            type="button"
                                            onclick="openDeleteModal('{{ route('admin.services.destroy', $service) }}', '{{ addslashes($service->name) }}')"
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
                {{ $services->links() }}
            </div>
        @endif
    </div>
@endsection
