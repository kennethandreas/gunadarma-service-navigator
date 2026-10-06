@props(['hover' => false])

<div {{ $attributes->merge(['class' => $hover ? 'card-hover' : 'card']) }}>
    {{ $slot }}
</div>
