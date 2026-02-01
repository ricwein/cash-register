<?php
declare(strict_types=1);

namespace App\Entity;

use Stringable;

interface TaxedEntityInterface
{
    public function getTax(): null|string|int|Stringable;
}
