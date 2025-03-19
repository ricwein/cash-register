<?php

namespace App\Entity;

use App\Repository\SettingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingRepository::class)]
class Setting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column]
    private bool $isOn = true;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    public static function create(string $name, bool $value, ?string $description = null): self
    {
        return (new self())
            ->setName($name)
            ->setIsOn($value)
            ->setDescription($description);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isOn(): bool
    {
        return $this->isOn;
    }

    public function setIsOn(bool $isOn): static
    {
        $this->isOn = $isOn;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
