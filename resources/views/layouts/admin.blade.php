@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => '📊'],
        ['label' => 'Kategori', 'route' => 'admin.categories.index', 'icon' => '🗂️'],
        ['label' => 'Layanan', 'route' => 'admin.services.index', 'icon' => '🔗'],
        ['label' => 'Riwayat Pertanyaan', 'route' => 'admin.search-histories.index', 'icon' => '🕘'],
        ['label' => 'Dataset', 'route' => 'admin.dataset.index', 'icon' => '📁'],
        ['label' => 'Model AI', 'route' => 'admin.model.index', 'icon' => '🤖'],
        ['label' => 'Pengaturan', 'route' => 'admin.settings.index', 'icon' => '⚙️'],
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') — Gunadarma Academic Service Navigator</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-background text-text antialiased">
    <div class="flex min-h-screen">

        {{-- Sidebar (desktop) --}}
        <aside class="hidden w-64 shrink-0 border-r border-border bg-surface lg:flex lg:flex-col">
            <div class="border-b border-border px-5 py-4">
                <p class="text-sm font-semibold text-text leading-tight">Gunadarma Academic<br>Service Navigator</p>
                <p class="text-xs text-text-muted">Admin Panel</p>
            </div>

            <nav class="flex-1 space-y-1 px-3 py-4">
                @foreach ($navItems as $item)
                    @if (Route::has($item['route']))
                        <a
                            href="{{ route($item['route']) }}"
                            class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium
                                {{ request()->routeIs($item['route'].'*') ? 'bg-primary-light text-primary' : 'text-text-muted hover:bg-background hover:text-text' }}"
                        >
                            <span aria-hidden="true">{{ $item['icon'] }}</span>
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="flex items-center justify-between gap-3 rounded-lg px-3 py-2 text-sm font-medium text-text-muted/50">
                            <span class="flex items-center gap-3">
                                <span aria-hidden="true">{{ $item['icon'] }}</span>
                                {{ $item['label'] }}
                            </span>
                            <span class="badge-neutral text-[10px]">Segera</span>
                        </span>
                    @endif
                @endforeach
            </nav>

            <div class="border-t border-border p-3">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-text-muted hover:bg-background hover:text-text">
                        <span aria-hidden="true">🚪</span>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            {{-- Topbar --}}
            <header class="flex items-center justify-between border-b border-border bg-surface px-4 py-3 lg:px-8">
                <div class="flex items-center gap-3">
                    <button
                        id="admin-nav-toggle"
                        type="button"
                        class="rounded-lg p-2 text-text-muted hover:bg-background lg:hidden"
                        aria-label="Buka menu"
                        aria-expanded="false"
                        aria-controls="admin-mobile-nav"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-sm font-semibold text-text">@yield('title', 'Admin')</h1>
                </div>

                <span class="text-sm text-text-muted">{{ auth()->user()->name }}</span>
            </header>

            {{-- Mobile nav drawer --}}
            <nav id="admin-mobile-nav" class="hidden flex-col gap-1 border-b border-border bg-surface px-3 py-3 lg:hidden">
                @foreach ($navItems as $item)
                    @if (Route::has($item['route']))
                        <a href="{{ route($item['route']) }}" class="rounded-lg px-3 py-2 text-sm font-medium text-text hover:bg-background">
                            {{ $item['icon'] }} {{ $item['label'] }}
                        </a>
                    @else
                        <span class="flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium text-text-muted/50">
                            <span>{{ $item['icon'] }} {{ $item['label'] }}</span>
                            <span class="badge-neutral text-[10px]">Segera</span>
                        </span>
                    @endif
                @endforeach
                <form method="POST" action="{{ route('admin.logout') }}" class="mt-1">
                    @csrf
                    <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-text-muted hover:bg-background">
                        🚪 Logout
                    </button>
                </form>
            </nav>

            <main class="flex-1 px-4 py-6 lg:px-8 lg:py-8">
                @yield('content')
            </main>
        </div>
    </div>

    <x-toast />

    {{-- Shared delete-confirmation modal — opened via openDeleteModal(url, label) --}}
    <div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center px-4">
        <div class="absolute inset-0 bg-text/40" onclick="closeDeleteModal()"></div>
        <div class="card relative w-full max-w-sm p-6">
            <h3 class="heading-3">Hapus data ini?</h3>
            <p class="mt-2 text-sm text-text-muted">
                <span id="delete-modal-label" class="font-medium text-text"></span> akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.
            </p>
            <form id="delete-modal-form" method="POST" class="mt-6 flex justify-end gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeDeleteModal()" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary bg-error hover:bg-error focus:ring-error">Hapus</button>
            </form>
        </div>
    </div>

    <script>
        const adminNavToggle = document.getElementById('admin-nav-toggle');
        const adminMobileNav = document.getElementById('admin-mobile-nav');

        adminNavToggle?.addEventListener('click', () => {
            const isOpen = !adminMobileNav.classList.contains('hidden');
            adminMobileNav.classList.toggle('hidden');
            adminMobileNav.classList.toggle('flex');
            adminNavToggle.setAttribute('aria-expanded', String(!isOpen));
        });

        function openDeleteModal(url, label) {
            document.getElementById('delete-modal-form').action = url;
            document.getElementById('delete-modal-label').textContent = label;
            const modal = document.getElementById('delete-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('delete-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Disable the submit button on forms marked data-loading-form so
        // admins get feedback while the request is in flight and can't
        // double-submit.
        document.querySelectorAll('form[data-loading-form]').forEach((form) => {
            form.addEventListener('submit', () => {
                const button = form.querySelector('button[type="submit"]');
                if (button) {
                    button.disabled = true;
                    button.dataset.originalText = button.textContent;
                    button.textContent = 'Menyimpan...';
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
