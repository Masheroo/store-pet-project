<?php

namespace App\Entity\Discount;

use App\Entity\City;
use App\Repository\CityDiscountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CityDiscountRepository::class)]
class CityDiscount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'cityDiscounts')]
        #[ORM\JoinColumn(nullable: false)]
        private City $City,

        #[ORM\Column]
        private float $discount
    )
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCity(): ?City
    {
        return $this->City;
    }

    public function setCity(?City $City): static
    {
        $this->City = $City;

        return $this;
    }

    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    public function setDiscount(float $discount): static
    {
        $this->discount = $discount;

        return $this;
    }
}
