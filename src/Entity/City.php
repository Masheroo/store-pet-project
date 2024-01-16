<?php

namespace App\Entity;

use App\Entity\Discount\CityDiscount;
use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CityRepository::class)]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'City', targetEntity: CityDiscount::class)]
    private Collection $cityDiscounts;

    public function __construct(
        #[ORM\Column(length: 255)]
        private ?string $name = null,

        #[ORM\OneToMany(mappedBy: 'city', targetEntity: User::class)]
        private Collection $users = new ArrayCollection()
    ) {
        $this->cityDiscounts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            if ($user->getCity() === $this) {
                $user->setCity(null);
            }
        }

        return $this;
    }
}
