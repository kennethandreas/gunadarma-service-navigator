@props(['variant' => 'success'])

@php
    $classes = match ($variant) {
        'warning' => 'alert-warning',
        'error' => 'alert-error',
        default => 'alert-success',
    };
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} role="alert">
    {{ $slot }}
</div>
