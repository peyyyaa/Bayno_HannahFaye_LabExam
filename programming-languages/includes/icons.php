<?php
// Inline SVG icons matching the ones in the Figma components.
// They use currentColor, so CSS controls their color.

function icon(string $name, int $size = 18): string
{
    $paths = [
        'lock'  => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
        'eye'   => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
        'eye-off' => '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>',
        'check' => '<polyline points="20 6 9 17 4 12"/>',
        'info'  => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>',
        'x'     => '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>',
    ];

    if ($name === 'error') {
        // Filled red circle with "!" (Material "error" icon)
        return '<svg class="icon icon-error" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" aria-hidden="true">'
             . '<circle cx="12" cy="12" r="10" fill="currentColor"/>'
             . '<rect x="11" y="6.5" width="2" height="7.5" rx="1" fill="#fff"/><circle cx="12" cy="17" r="1.2" fill="#fff"/></svg>';
    }

    return '<svg class="icon icon-' . $name . '" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" '
         . 'stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
         . $paths[$name] . '</svg>';
}
