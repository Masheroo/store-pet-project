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
        private readonly City $city,

        #[ORM\Column]
        private readonly float $discount
    )
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiscount(): ?float
    {
        return $this->discount;
    }
}
