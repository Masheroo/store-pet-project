<?php

namespace App\Entity\Discount;

use App\Entity\User;
use App\Repository\UserDiscountRepository;
use App\Type\DiscountType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserDiscountRepository::class)]
class UserDiscount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'userDiscounts')]
        private User $user,
        #[ORM\Column]
        private float $discount,
        #[ORM\Column(type: 'integer', enumType: DiscountType::class)]
        private DiscountType $type
    )
    {
    }

    public function getType(): DiscountType
    {
        return $this->type;
    }

    public function setType(DiscountType $type): void
    {
        $this->type = $type;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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
