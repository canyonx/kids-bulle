<?php

namespace App\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TextColorExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('contrast_text', [$this, 'getContrastTextColor']),
        ];
    }

    public function getContrastTextColor(string $hexColor): string
    {
        $hexColor = ltrim($hexColor, '#');

        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));

        $luminance = 0.299 * $r + 0.587 * $g + 0.114 * $b;

        return $luminance > 186 ? 'light' : 'dark';
    }
}
