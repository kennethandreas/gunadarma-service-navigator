@extends('layouts.admin')

@section('title', $row ? 'Edit Contoh Pertanyaan' : 'Tambah Contoh Pertanyaan')

@section('content')
    <a href="{{ route('admin.dataset.index') }}" class="text-sm font-medium text-text-muted hover:text-primary">&larr; Dataset</a>

    <h2 class="heading-2 mt-2">{{ $row ? 'Edit Contoh Pertanyaan' : 'Tambah Contoh Pertanyaan' }}</h2>

    <div class="card mt-6 max-w-xl p-6">
        <form
            method="POST"
            action="{{ $row ? route('admin.dataset.update', $row['id']) : route('admin.dataset.store') }}"
            data-loading-form
            class="space-y-5"
        >
            @csrf
            @if ($row)
                @method('PUT')
            @endif

            <div>
                <label for="text" class="form-label">Teks Pertanyaan</label>
                <textarea id="text" name="text" rows="3" required class="form-input">{{ old('text', $row['text'] ?? '') }}</textarea>
                <p class="mt-1 text-xs text-text-muted">Tulis dalam bahasa sehari-hari, seperti pertanyaan asli yang mungkin diketik pengguna.</p>
                @error('text')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="intent" class="form-label">Intent</label>
                <select id="intent" name="intent" required class="form-input">
                    <option value="" disabled @selected(old('intent', $row['intent'] ?? '') === '')>Pilih intent...</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected(old('intent', $row['intent'] ?? '') === $category->slug)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-text-muted">Kategori/intent yang paling sesuai dengan maksud pertanyaan ini.</p>
                @error('intent')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.dataset.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    {{ $row ? 'Simpan Perubahan' : 'Tambah Contoh' }}
                </button>
            </div>
        </form>
    </div>
@endsection
