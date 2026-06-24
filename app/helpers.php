<?php

// app/helpers.php

if (!function_exists('adjustColor')) {
    /**
     * Darken or lighten a hex color by a given amount.
     * Negative = darker, positive = lighter.
     */
    function adjustColor(string $hex, int $amount): string
    {
        $hex = ltrim($hex, '#');

        $r = max(0, min(255, hexdec(substr($hex, 0, 2)) + $amount));
        $g = max(0, min(255, hexdec(substr($hex, 2, 2)) + $amount));
        $b = max(0, min(255, hexdec(substr($hex, 4, 2)) + $amount));

        return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT)
                    . str_pad(dechex($g), 2, '0', STR_PAD_LEFT)
                    . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }
}
