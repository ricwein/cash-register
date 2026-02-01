<?php
declare(strict_types=1);

namespace App\Entity;

interface CountableEntityInterface
{
    public function getQuantity(): int;
}
