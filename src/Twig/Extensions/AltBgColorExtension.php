<?php

namespace App\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AltBgColorExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('alt_bg_color', [$this, 'altBgColor']),
        ];
    }

    public function altBgColor(string $hex): string
    {
        $hex = ltrim($hex);

        return $hex . '22';
    }
}
