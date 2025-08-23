<?php

namespace App\Entity;

use App\Repository\PurchasedArticleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Stringable;

#[ORM\Entity(repositoryClass: PurchasedArticleRepository::class)]
#[ORM\Index(columns: ['product_id'])]
class PurchasedArticle implements Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $productId = null;

    #[ORM\Column(length: 255)]
    private ?string $productName = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'soldArticles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PurchaseTransaction $receiptTransaction = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): static
    {
        $this->productId = $productId;

        return $this;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): static
    {
        $this->productName = $productName;

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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getReceiptTransaction(): ?PurchaseTransaction
    {
        return $this->receiptTransaction;
    }

    public function setReceiptTransaction(?PurchaseTransaction $receiptTransaction): static
    {
        $this->receiptTransaction = $receiptTransaction;

        return $this;
    }

    public function __toString()
    {
        return $this->getProductName() ?? '';
    }
}
