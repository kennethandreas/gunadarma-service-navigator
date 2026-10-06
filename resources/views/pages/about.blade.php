@extends('layouts.app')

@section('title', 'Tentang — Gunadarma Academic Service Navigator')

@section('content')

    <section class="mx-auto max-w-2xl px-4 py-16 sm:px-6">
        <h1 class="heading-1">Tentang Gunadarma Academic Service Navigator</h1>

        <div class="mt-8 space-y-4 text-body text-text">
            <h2 class="heading-3">Apa itu Gunadarma Academic Service Navigator?</h2>
            <p>
                Sistem ini membantu pengguna menemukan layanan akademik yang sesuai berdasarkan
                kebutuhan yang ditulis menggunakan bahasa natural.
            </p>
            <p>
                Sistem menggunakan machine learning untuk mengidentifikasi intent pertanyaan,
                kemudian menghubungkannya dengan layanan resmi yang relevan.
            </p>
        </div>

        <div class="mt-10">
            <h2 class="heading-3">Cara kerjanya</h2>
            <ol class="mt-4 space-y-3">
                <li class="card flex gap-3 p-4">
                    <span class="badge-primary shrink-0">1</span>
                    <span class="text-sm text-text">Tulis kebutuhanmu dengan bahasa sehari-hari, misalnya "saya mau cek jadwal kuliah".</span>
                </li>
                <li class="card flex gap-3 p-4">
                    <span class="badge-primary shrink-0">2</span>
                    <span class="text-sm text-text">Sistem mengenali maksud (intent) dari pertanyaanmu.</span>
                </li>
                <li class="card flex gap-3 p-4">
                    <span class="badge-primary shrink-0">3</span>
                    <span class="text-sm text-text">Layanan yang sesuai ditampilkan, lengkap dengan link resmi Universitas Gunadarma.</span>
                </li>
            </ol>
        </div>

        <p class="mt-10 text-sm text-text-muted">
            Navigator ini bukan pengganti situs resmi Universitas Gunadarma — sistem ini membantu kamu
            menemukan layanan resmi yang sudah tersedia, lebih cepat.
        </p>
    </section>

@endsection
