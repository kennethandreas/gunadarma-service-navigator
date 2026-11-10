<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#6d28d9">
    <title>@yield('title', 'Gunadarma Academic Service Navigator')</title>
    <meta name="description" content="Temukan layanan kampus yang kamu butuhkan — tulis pertanyaanmu, sistem akan membantu menemukan layanan Gunadarma yang paling sesuai.">
    <link rel="icon" href="/favicon.ico" sizes="any">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-background text-text antialiased">

    <header class="sticky top-0 z-40 border-b border-border bg-surface/80 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6">
            <a href="/" class="text-sm font-semibold text-text sm:text-base">
                Gunadarma <span class="text-primary">Academic Service Navigator</span>
            </a>

            <nav class="hidden items-center gap-8 text-sm font-medium text-text-muted md:flex">
                <a href="/" class="hover:text-primary {{ request()->is('/') ? 'text-primary' : '' }}">Beranda</a>
                <a href="/layanan" class="hover:text-primary {{ request()->is('layanan*') ? 'text-primary' : '' }}">Layanan</a>
                <a href="/tentang" class="hover:text-primary {{ request()->is('tentang') ? 'text-primary' : '' }}">Tentang</a>
            </nav>

            <div class="hidden md:block">
                <x-button href="/#cari" variant="primary">Cari Layanan</x-button>
            </div>

            <button
                id="nav-toggle"
                type="button"
                class="rounded-lg p-2 text-text-muted hover:bg-background md:hidden"
                aria-label="Buka menu"
                aria-expanded="false"
                aria-controls="mobile-nav"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <nav id="mobile-nav" class="hidden flex-col gap-1 border-t border-border bg-surface px-4 py-3 md:hidden">
            <a href="/" class="rounded-lg px-3 py-2 text-sm font-medium text-text hover:bg-background">Beranda</a>
            <a href="/layanan" class="rounded-lg px-3 py-2 text-sm font-medium text-text hover:bg-background">Layanan</a>
            <a href="/tentang" class="rounded-lg px-3 py-2 text-sm font-medium text-text hover:bg-background">Tentang</a>
            <a href="/#cari" class="mt-1 rounded-lg bg-primary px-3 py-2 text-center text-sm font-medium text-white hover:bg-primary-hover">Cari Layanan</a>
        </nav>
    </header>

    <div>
        @yield('content')
    </div>

    <footer class="border-t border-border bg-surface">
        <div class="mx-auto max-w-6xl px-4 py-8 text-sm text-text-muted sm:px-6">
            <p>&copy; {{ date('Y') }} Gunadarma Academic Service Navigator. Bukan situs resmi — navigator menuju layanan resmi Universitas Gunadarma.</p>
        </div>
    </footer>

    <script>
        const navToggle = document.getElementById('nav-toggle');
        const mobileNav = document.getElementById('mobile-nav');

        navToggle?.addEventListener('click', () => {
            const isOpen = !mobileNav.classList.contains('hidden');
            mobileNav.classList.toggle('hidden');
            mobileNav.classList.toggle('flex');
            navToggle.setAttribute('aria-expanded', String(!isOpen));
        });
    </script>

    @stack('scripts')
</body>
</html>
