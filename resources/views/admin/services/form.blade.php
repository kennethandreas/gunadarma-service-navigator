@extends('layouts.admin')

@section('title', $service->exists ? 'Edit Layanan' : 'Tambah Layanan')

@section('content')
    <a href="{{ route('admin.services.index') }}" class="text-sm font-medium text-text-muted hover:text-primary">&larr; Layanan</a>

    <h2 class="heading-2 mt-2">{{ $service->exists ? 'Edit Layanan' : 'Tambah Layanan' }}</h2>

    <div class="card mt-6 max-w-xl p-6">
        <form
            method="POST"
            action="{{ $service->exists ? route('admin.services.update', $service) : route('admin.services.store') }}"
            data-loading-form
            class="space-y-5"
        >
            @csrf
            @if ($service->exists)
                @method('PUT')
            @endif

            <div>
                <label for="category_id" class="form-label">Kategori</label>
                <select id="category_id" name="category_id" required class="form-input">
                    <option value="">Pilih kategori...</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) old('category_id', $service->category_id) === $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="name" class="form-label">Nama Layanan</label>
                <input id="name" type="text" name="name" value="{{ old('name', $service->name) }}" required class="form-input">
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug" class="form-label">Slug <span class="text-text-muted font-normal">(opsional, dibuat otomatis dari nama)</span></label>
                <input id="slug" type="text" name="slug" value="{{ old('slug', $service->slug) }}" class="form-input">
                @error('slug')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="form-label">Deskripsi</label>
                <textarea id="description" name="description" rows="3" class="form-input">{{ old('description', $service->description) }}</textarea>
                @error('description')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="capabilities" class="form-label">Apa yang bisa dilakukan? <span class="text-text-muted font-normal">(satu per baris)</span></label>
                <textarea id="capabilities" name="capabilities" rows="4" class="form-input">{{ old('capabilities', $service->capabilities ? implode("\n", $service->capabilities) : '') }}</textarea>
                @error('capabilities')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="url" class="form-label">URL Resmi</label>
                <input id="url" type="url" name="url" value="{{ old('url', $service->url) }}" placeholder="https://..." class="form-input">
                @error('url')
                    <p class="form-error">{{ $message }}</p>
                @enderror
                <p class="mt-1.5 text-xs text-text-muted">Gunakan link resmi Universitas Gunadarma. Boleh diisi placeholder dulu jika belum tersedia.</p>
            </div>

            <label class="flex items-center gap-2 text-sm text-text">
                <input
                    type="checkbox"
                    name="status"
                    value="1"
                    class="rounded border-border text-primary focus:ring-primary"
                    @checked(old('status', $service->exists ? $service->status : true))
                >
                Aktifkan layanan ini
            </label>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.services.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    {{ $service->exists ? 'Simpan Perubahan' : 'Tambah Layanan' }}
                </button>
            </div>
        </form>
    </div>
@endsection
