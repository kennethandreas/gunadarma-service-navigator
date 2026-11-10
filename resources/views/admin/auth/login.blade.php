<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin — Gunadarma Academic Service Navigator</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-background px-4 antialiased">
    <div class="w-full max-w-sm">
        <div class="mb-6 text-center">
            <p class="text-sm font-semibold text-text">Gunadarma Academic Service Navigator</p>
            <p class="text-sm text-text-muted">Login Admin</p>
        </div>

        <div class="card p-6">
            @if ($errors->any())
                <x-alert variant="error" class="mb-4">
                    {{ $errors->first() }}
                </x-alert>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-input">
                </div>

                <div>
                    <label for="password" class="form-label">Password</label>
                    <input id="password" type="password" name="password" required class="form-input">
                </div>

                <label class="flex items-center gap-2 text-sm text-text-muted">
                    <input type="checkbox" name="remember" class="rounded border-border text-primary focus:ring-primary">
                    Ingat saya
                </label>

                <x-button type="submit" variant="primary" class="w-full">
                    Masuk
                </x-button>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-text-muted">
            <a href="{{ url('/') }}" class="hover:text-text">&larr; Kembali ke situs utama</a>
        </p>
    </div>
</body>
</html>
