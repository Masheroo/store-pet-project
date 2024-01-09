<?php

namespace App\Entity;

use App\Repository\VolumeDiscountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VolumeDiscountRepository::class)]
class VolumeDiscount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function __construct(
        #[ORM\Column] private int $amount,
        #[ORM\Column] private float $discount,
    )
    {
    }

    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function setDiscount(float $discount): void
    {
        $this->discount = $discount;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }
}
