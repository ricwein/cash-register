<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Attribute\AsTwigFilter;

final readonly class AppExtension
{
    #[AsTwigFilter('string')]
    public function convertToNumber(mixed $number): string
    {
        return (string)$number;
    }
}
