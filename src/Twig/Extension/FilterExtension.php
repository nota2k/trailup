<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\FilterExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class FilterExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('distance_filter', [$this, 'filterByDistance']),
        ];
    }

    public function filterByDistance($value): array
    {
        
        return [
            new TwigFunction('function_name', [FilterExtensionRuntime::class, 'doSomething']),
        ];
    }
}
