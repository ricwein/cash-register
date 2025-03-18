<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Stringable;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event implements Stringable
{
    private const DEFAULT_PRODUCTS_PER_ROW = 5;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $priority = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private int $productsPerRow = self::DEFAULT_PRODUCTS_PER_ROW;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'event', cascade: ['persist'], orphanRemoval: true)]
    private Collection $products;

    #[ORM\Column]
    private bool $useCategoryTabs = true;

    public function __construct()
    {
        $this->products = new ArrayCollection();
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

    public function getProductsPerRow(): int
    {
        return $this->productsPerRow;
    }

    public function setProductsPerRow(int $productsPerRow): static
    {
        $this->productsPerRow = $productsPerRow;
        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setEvent($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getEvent() === $this) {
                $product->setEvent(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName() ?? '';
    }

    public function isUseCategoryTabs(): ?bool
    {
        return $this->useCategoryTabs;
    }

    public function setUseCategoryTabs(bool $useCategoryTabs): static
    {
        $this->useCategoryTabs = $useCategoryTabs;

        return $this;
    }

}
