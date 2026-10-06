@props(['service', 'icon' => null])

<a href="/layanan/{{ $service->slug }}" class="card-hover flex flex-col gap-3 p-5">
    <span class="text-2xl" aria-hidden="true">{{ $icon ?? $service->category->icon() }}</span>

    <div>
        <h3 class="heading-3">{{ $service->name }}</h3>
        @if ($service->description)
            <p class="mt-1 text-sm text-text-muted line-clamp-2">{{ $service->description }}</p>
        @endif
    </div>

    <span class="mt-auto inline-flex items-center gap-1 text-sm font-medium text-primary">
        Lihat detail
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
        </svg>
    </span>
</a>
