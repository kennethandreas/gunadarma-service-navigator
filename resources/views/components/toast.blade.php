@if (session('success') || session('error'))
    <div
        id="app-toast"
        class="fixed bottom-4 right-4 z-50 flex items-start gap-2 rounded-xl border px-4 py-3 text-sm shadow-lg
            {{ session('success') ? 'border-success/20 bg-success-light text-success' : 'border-error/20 bg-error-light text-error' }}"
        role="status"
    >
        <span aria-hidden="true">{{ session('success') ? '✅' : '⚠️' }}</span>
        <span>{{ session('success') ?? session('error') }}</span>
    </div>

    <script>
        setTimeout(() => {
            document.getElementById('app-toast')?.remove();
        }, 3500);
    </script>
@endif
