<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Attribute\AsTwigFilter;
use Twig\Attribute\AsTwigTest;

final readonly class AppExtension
{
    #[AsTwigFilter('string')]
    public function convertToNumber(mixed $number): string
    {
        return (string)$number;
    }

    #[AsTwigTest('instanceof')]
    public function isInstanceOf(mixed $object, string $class): bool
    {
        if (is_object($object)) {
            return $object instanceof $class;
        }

        if (is_string($object)) {
            return is_a($object, $class, true);
        }

        return false;
    }

    #[AsTwigTest('numeric')]
    public function isNumeric(mixed $value): bool
    {
        return is_numeric($value);
    }
}
