<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private float $fullPrice;
    #[ORM\Column]
    private float $lotCost;

    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'orders')]
        private User $user,
        #[ORM\ManyToOne(inversedBy: 'orders')]
        private Lot $lot,
        #[ORM\Column]
        private int $quantity,
        #[ORM\Column]
        private float $discount = 0,
    ) {
        $this->fullPrice = $this->lot->getCost() * $this->quantity;
        $this->lotCost = $this->lot->getCost();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function setDiscount(float $discount): static
    {
        $this->discount = $discount;

        return $this;
    }

    public function getLot(): Lot
    {
        return $this->lot;
    }

    public function setLot(Lot $lot): static
    {
        $this->lot = $lot;

        return $this;
    }

    public function getFullPrice(): float
    {
        return $this->fullPrice;
    }
}
