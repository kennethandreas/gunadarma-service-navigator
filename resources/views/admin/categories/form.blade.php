@extends('layouts.admin')

@section('title', $category->exists ? 'Edit Kategori' : 'Tambah Kategori')

@section('content')
    <a href="{{ route('admin.categories.index') }}" class="text-sm font-medium text-text-muted hover:text-primary">&larr; Kategori</a>

    <h2 class="heading-2 mt-2">{{ $category->exists ? 'Edit Kategori' : 'Tambah Kategori' }}</h2>

    <div class="card mt-6 max-w-xl p-6">
        <form
            method="POST"
            action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
            data-loading-form
            class="space-y-5"
        >
            @csrf
            @if ($category->exists)
                @method('PUT')
            @endif

            <div>
                <label for="name" class="form-label">Nama Kategori</label>
                <input id="name" type="text" name="name" value="{{ old('name', $category->name) }}" required class="form-input">
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug" class="form-label">Slug <span class="text-text-muted font-normal">(opsional, dibuat otomatis dari nama)</span></label>
                <input id="slug" type="text" name="slug" value="{{ old('slug', $category->slug) }}" class="form-input">
                @error('slug')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="form-label">Deskripsi</label>
                <textarea id="description" name="description" rows="3" class="form-input">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-text">
                <input
                    type="checkbox"
                    name="status"
                    value="1"
                    class="rounded border-border text-primary focus:ring-primary"
                    @checked(old('status', $category->exists ? $category->status : true))
                >
                Aktifkan kategori ini
            </label>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.categories.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    {{ $category->exists ? 'Simpan Perubahan' : 'Tambah Kategori' }}
                </button>
            </div>
        </form>
    </div>
@endsection
