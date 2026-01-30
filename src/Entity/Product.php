<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $priority = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotNull]
    private ?string $price = null;

    #[ORM\Column(length: 7, nullable: true, options: ['fixed' => true])]
    private ?string $color = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $icon = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[Assert\NotBlank]
    private ?Category $category = null;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\ManyToMany(targetEntity: Event::class, inversedBy: 'products')]
    private Collection $events;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class)]
    #[Assert\Expression(
        '!value.contains(this.getCategory())',
        message: 'Category already set as primary category',
    )]
    private Collection $additionalCategories;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private SalesTax $tax;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->additionalCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): static
    {
        $this->priority = $priority;
        return $this;
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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = ($color === '#000000') ? null : $color;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        $this->events->removeElement($event);

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getAdditionalCategories(): Collection
    {
        return $this->additionalCategories;
    }

    public function addAdditionalCategory(Category $additionalCategory): static
    {
        if (!$this->additionalCategories->contains($additionalCategory)) {
            $this->additionalCategories->add($additionalCategory);
        }

        return $this;
    }

    public function removeAdditionalCategory(Category $additionalCategory): static
    {
        $this->additionalCategories->removeElement($additionalCategory);

        return $this;
    }

    public function getTax(): ?SalesTax
    {
        return $this->tax ?? null;
    }

    public function setTax(SalesTax $tax): static
    {
        $this->tax = $tax;

        return $this;
    }
}
