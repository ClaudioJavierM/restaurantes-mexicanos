@props([
    'type' => 'Restaurant',
    'data' => [],
])

@php
$schema = [
    '@context' => 'https://schema.org',
    '@type' => $type,
];

$schema = array_merge($schema, $data);
@endphp

<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
