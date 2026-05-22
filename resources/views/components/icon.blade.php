@props([
    'name',
    'size' => 'h-5 w-5',
    'stroke' => '2',
])

@php
    $paths = [
        'activity' => '<path d="M22 12h-4l-3 9L9 3l-3 9H2" />',
        'alert-triangle' => '<path d="m21.73 18-8-14a2 2 0 0 0-3.46 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z" /><path d="M12 9v4" /><path d="M12 17h.01" />',
        'bell' => '<path d="M10.27 21a2 2 0 0 0 3.46 0" /><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" />',
        'camera' => '<path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3Z" /><circle cx="12" cy="13" r="3" />',
        'chart' => '<path d="M3 3v18h18" /><path d="M18 17V9" /><path d="M13 17V5" /><path d="M8 17v-3" />',
        'check' => '<path d="M20 6 9 17l-5-5" />',
        'check-circle' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" /><path d="m9 11 3 3L22 4" />',
        'chevron-right' => '<path d="m9 18 6-6-6-6" />',
        'circle-help' => '<circle cx="12" cy="12" r="10" /><path d="M9.09 9a3 3 0 1 1 5.83 1c0 2-3 2-3 4" /><path d="M12 17h.01" />',
        'copy' => '<rect width="14" height="14" x="8" y="8" rx="2" /><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" />',
        'credit-card' => '<rect width="20" height="14" x="2" y="5" rx="2" /><path d="M2 10h20" />',
        'file-text' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" /><path d="M14 2v6h6" /><path d="M16 13H8" /><path d="M16 17H8" /><path d="M10 9H8" />',
        'info' => '<circle cx="12" cy="12" r="10" /><path d="M12 16v-4" /><path d="M12 8h.01" />',
        'landmark' => '<path d="m3 21 18 0" /><path d="M5 21V10" /><path d="M19 21V10" /><path d="M9 21V10" /><path d="M15 21V10" /><path d="M3 10h18" /><path d="m12 3 8 4H4Z" />',
        'key' => '<path d="m15.5 7.5 1.5-1.5" /><path d="m18 4 2 2" /><path d="M7 14a4 4 0 1 1 5.66-5.66L21 16.68V21h-4.32l-8.34-8.34A4 4 0 0 1 7 14Z" />',
        'layout-dashboard' => '<rect width="7" height="9" x="3" y="3" rx="1" /><rect width="7" height="5" x="14" y="3" rx="1" /><rect width="7" height="9" x="14" y="12" rx="1" /><rect width="7" height="5" x="3" y="16" rx="1" />',
        'lifebuoy' => '<circle cx="12" cy="12" r="10" /><circle cx="12" cy="12" r="4" /><path d="m4.93 4.93 4.24 4.24" /><path d="m14.83 14.83 4.24 4.24" /><path d="m14.83 9.17 4.24-4.24" /><path d="m4.93 19.07 4.24-4.24" />',
        'link' => '<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" /><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />',
        'list-checks' => '<path d="m3 17 2 2 4-4" /><path d="m3 7 2 2 4-4" /><path d="M13 6h8" /><path d="M13 12h8" /><path d="M13 18h8" />',
        'lock' => '<rect width="18" height="11" x="3" y="11" rx="2" /><path d="M7 11V7a5 5 0 0 1 10 0v4" />',
        'pen-line' => '<path d="M12 20h9" /><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />',
        'qr-code' => '<rect width="5" height="5" x="3" y="3" rx="1" /><rect width="5" height="5" x="16" y="3" rx="1" /><rect width="5" height="5" x="3" y="16" rx="1" /><path d="M21 16h-3a2 2 0 0 0-2 2v3" /><path d="M21 21v.01" /><path d="M12 7v3a2 2 0 0 1-2 2H7" /><path d="M3 12h.01" /><path d="M12 3h.01" /><path d="M12 16v.01" /><path d="M16 12h1" /><path d="M21 12v.01" /><path d="M12 21v-1" />',
        'receipt' => '<path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z" /><path d="M16 8H8" /><path d="M16 12H8" /><path d="M10 16H8" />',
        'shield-check' => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67 0C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.5 3.8 17 5 19 5a1 1 0 0 1 1 1Z" /><path d="m9 12 2 2 4-4" />',
        'store' => '<path d="m2 7 1.4-4.2A1.2 1.2 0 0 1 4.54 2h14.92a1.2 1.2 0 0 1 1.14.8L22 7" /><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" /><path d="M10 22v-6h4v6" /><path d="M2 7h20" /><path d="M6 7v2" /><path d="M12 7v2" /><path d="M18 7v2" />',
        'ticket' => '<path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z" /><path d="M13 5v2" /><path d="M13 17v2" /><path d="M13 11v2" />',
        'x' => '<path d="M18 6 6 18" /><path d="m6 6 12 12" />',
        'x-circle' => '<circle cx="12" cy="12" r="10" /><path d="m15 9-6 6" /><path d="m9 9 6 6" />',
    ];

    $path = $paths[$name] ?? $paths['circle-help'];
@endphp

<svg {{ $attributes->merge(['class' => $size]) }}
     xmlns="http://www.w3.org/2000/svg"
     viewBox="0 0 24 24"
     fill="none"
     stroke="currentColor"
     stroke-width="{{ $stroke }}"
     stroke-linecap="round"
     stroke-linejoin="round"
     aria-hidden="true">
    {!! $path !!}
</svg>
