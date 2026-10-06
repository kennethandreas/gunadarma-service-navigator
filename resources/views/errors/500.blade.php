@extends('layouts.app')

@section('title', 'Terjadi Kesalahan — Gunadarma Academic Service Navigator')

@section('content')

    <section class="mx-auto flex max-w-xl flex-col items-center px-4 py-24 text-center sm:px-6">
        <span class="text-5xl" aria-hidden="true">⚠️</span>
        <h1 class="heading-1 mt-4">Terjadi kesalahan di sistem kami</h1>
        <p class="mt-3 text-body-muted">
            Maaf, ada yang tidak berjalan semestinya di server. Ini bukan kesalahanmu — coba muat ulang
            halaman ini sebentar lagi, atau kembali ke beranda.
        </p>

        <div class="mt-8">
            <x-button href="/" variant="primary">Kembali ke Beranda</x-button>
        </div>
    </section>

@endsection
