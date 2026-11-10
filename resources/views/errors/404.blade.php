@extends('layouts.app')

@section('title', 'Halaman Tidak Ditemukan — Gunadarma Academic Service Navigator')

@section('content')

    <section class="mx-auto flex max-w-xl flex-col items-center px-4 py-24 text-center sm:px-6">
        <span class="text-5xl" aria-hidden="true">🧭</span>
        <h1 class="heading-1 mt-4">Halaman tidak ditemukan</h1>
        <p class="mt-3 text-body-muted">
            Halaman yang kamu cari mungkin sudah dipindahkan, dihapus, atau alamatnya salah ketik.
            Coba jelajahi seluruh layanan Gunadarma, atau kembali ke beranda untuk mencari lagi.
        </p>

        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
            <x-button href="/" variant="primary">Kembali ke Beranda</x-button>
            <x-button href="/layanan" variant="secondary">Lihat Semua Layanan</x-button>
        </div>
    </section>

@endsection
