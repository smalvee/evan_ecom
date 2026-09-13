<?php

namespace App\Support;

class HtmlSanitizer
{
    /**
     * Lightweight sanitizer for admin-authored rich text.
     *
     * Removes script/style/iframe/form blocks, inline event handlers and
     * javascript:/vbscript:/data: URLs while preserving normal formatting HTML.
     */
    public static function clean(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        // Remove dangerous element blocks entirely (including their contents).
        $html = preg_replace(
            '#<\s*(script|style|iframe|object|embed|form|link|meta|base)\b[^>]*>.*?<\s*/\s*\1\s*>#is',
            '',
            $html
        );

        // Remove stray / self-closing dangerous tags.
        $html = preg_replace(
            '#<\s*/?\s*(script|style|iframe|object|embed|form|link|meta|base)\b[^>]*>#is',
            '',
            $html
        );

        // Remove inline event handlers (onclick, onerror, onload, ...).
        $html = preg_replace('#\s+on\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#is', '', $html);

        // Neutralise javascript:/vbscript:/data: URLs in href/src.
        $html = preg_replace(
            '#((?:href|src)\s*=\s*)(["\'])\s*(?:javascript|vbscript|data):[^"\']*\2#is',
            '$1$2#$2',
            $html
        );

        return $html;
    }
}
