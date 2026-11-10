@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')
    <h2 class="heading-2">Pengaturan</h2>
    <p class="mt-1 text-body-muted">Konfigurasi sistem AI, disetel lewat file <code>.env</code>.</p>

    <div class="card mt-6 max-w-xl divide-y divide-border">
        <div class="p-5">
            <p class="text-sm font-medium text-text">Ambang Batas Confidence (AI_CONFIDENCE_THRESHOLD)</p>
            <p class="mt-1 text-sm text-text-muted">
                Hasil AI di bawah nilai ini tidak akan ditampilkan ke pengguna — sistem akan menampilkan pesan "belum yakin" alih-alih memaksakan jawaban.
            </p>
            <p class="mt-2 text-2xl font-semibold text-primary">{{ number_format($confidenceThreshold * 100, 0) }}%</p>
        </div>

        <div class="p-5">
            <p class="text-sm font-medium text-text">URL Layanan AI (AI_SERVICE_URL)</p>
            <p class="mt-1 text-sm text-text-muted">
                Alamat API Python (<code>ml/api.py</code>) yang dipanggil Laravel untuk klasifikasi intent.
            </p>
            <p class="mt-2 font-mono text-sm text-text">{{ $aiServiceUrl }}</p>
        </div>
    </div>

    <x-alert variant="warning" class="mt-6 max-w-xl">
        Untuk mengubah nilai di atas, edit file <code>.env</code> di root project lalu restart <code>php artisan serve</code>. Halaman ini tidak menyimpan perubahan ke database, jadi selalu mencerminkan konfigurasi server yang sebenarnya.
    </x-alert>
@endsection
