<?php
declare(strict_types=1);

namespace App\Entity;

/**
 * @author ricwein_privat
 * @since 01.02.2026
 */
interface CountableEntityInterface
{
    public function getQuantity(): int;
}
