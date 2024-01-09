<?php

namespace App\Entity;

use App\Repository\LotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LotRepository::class)]
class Lot
{
    #[ORM\OneToMany(mappedBy: 'lot', targetEntity: LotDiscount::class)]
    private Collection $lotDiscounts;

    /**
     * @param int|null    $id
     * @param string|null $title
     * @param float|null  $cost
     * @param int|null    $count
     * @param string|null $image
     */
    public function __construct(
        /** @noinspection PhpPropertyCanBeReadonlyInspection */
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column]
        private ?int $id = null,

        #[ORM\Column(length: 255)]
        private ?string $title = null,

        #[ORM\Column]
        private ?float $cost = null,

        #[ORM\Column]
        private ?int $count = null,

        #[ORM\Column(length: 255, nullable: true)]
        private ?string $image = null
    )
    {
        $this->lotDiscounts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCost(float $cost): static
    {
        $this->cost = $cost;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;

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
     * @return Collection<int, LotDiscount>
     */
    public function getLotDiscounts(): Collection
    {
        return $this->lotDiscounts;
    }
}
