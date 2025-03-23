<?php

namespace App\Entity;

use App\Enum\PaymentType;
use App\Repository\PurchaseTransactionRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PurchaseTransactionRepository::class)]
#[ORM\Index(columns: ['created_at', 'event_name'])]
class PurchaseTransaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $transactionId = null;

    #[ORM\Column(enumType: PaymentType::class)]
    private ?PaymentType $paymentType = null;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $price = '0.00';

    #[ORM\Column(length: 255)]
    private ?string $eventName = null;

    /**
     * @var Collection<int, PurchasedArticle>
     */
    #[ORM\OneToMany(targetEntity: PurchasedArticle::class, mappedBy: 'receiptTransaction', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $soldArticles;

    public function __construct()
    {
        $this->soldArticles = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransactionId(): ?Uuid
    {
        return $this->transactionId;
    }

    public function setTransactionId(Uuid $transactionId): static
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    public function getPaymentType(): ?PaymentType
    {
        return $this->paymentType;
    }

    public function setPaymentType(PaymentType $paymentType): static
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getEventName(): ?string
    {
        return $this->eventName;
    }

    public function setEventName(string $eventName): static
    {
        $this->eventName = $eventName;

        return $this;
    }

    /**
     * @return Collection<int, PurchasedArticle>
     */
    public function getSoldArticles(): Collection
    {
        return $this->soldArticles;
    }

    public function addSoldArticle(PurchasedArticle $soldArticle): static
    {
        if (!$this->soldArticles->contains($soldArticle)) {
            $this->soldArticles->add($soldArticle);
            $soldArticle->setReceiptTransaction($this);
        }

        return $this;
    }
}
