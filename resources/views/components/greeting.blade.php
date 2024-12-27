@php
    $hour = now()->format('H');
    $greeting = $hour < 12
        ? 'Good morning'
        : ($hour < 18 ? 'Good afternoon' : 'Good evening');
@endphp

<h1 class="text-4xl font-bold tracking-tight text-base-content sm:text-4xl">
    {{ $greeting }} {{ $slot }}
</h1>
