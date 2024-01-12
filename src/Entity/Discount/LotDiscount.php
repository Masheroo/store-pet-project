<?php

namespace App\Entity\Discount;

use App\Entity\Lot;
use App\Repository\LotDiscountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LotDiscountRepository::class)]
class LotDiscount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function __construct(
        #[ORM\Column]
        private int $countOfPurchases,

        #[ORM\ManyToOne(inversedBy: 'lotDiscounts')]
        #[ORM\JoinColumn(nullable: false)]
        private Lot $lot,

        #[ORM\Column]
        private float $discount
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountOfPurchases(): int
    {
        return $this->countOfPurchases;
    }

    public function setCountOfPurchases(int $countOfPurchases): static
    {
        $this->countOfPurchases = $countOfPurchases;

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

    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function setDiscount(float $discount): static
    {
        $this->discount = $discount;

        return $this;
    }
}
