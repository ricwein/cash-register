<?php

namespace App\Entity;

use App\Repository\SalesTaxRepository;
use Doctrine\ORM\Mapping as ORM;
use Stringable;

#[ORM\Entity(repositoryClass: SalesTaxRepository::class)]
class SalesTax implements Stringable
{
    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column]
        public ?int $id = null,

        #[ORM\Column(length: 255)]
        public string $name = '',

        #[ORM\Column]
        public int $percent = 0,
    ) {
    }

    public function __toString(): string
    {
        return $this->percent . '%';
    }
}
