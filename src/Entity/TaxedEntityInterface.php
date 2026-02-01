<?php
declare(strict_types=1);

namespace App\Entity;

use Stringable;

/**
 * @author ricwein_privat
 * @since 01.02.2026
 */
interface TaxedEntityInterface
{
    public function getTax(): null|string|int|Stringable;
}
