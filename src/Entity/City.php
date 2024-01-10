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
    #[ORM\OneToMany(mappedBy: 'City', targetEntity: CityDiscount::class)]
    private Collection $cityDiscounts;

    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column]
        private ?int $id = null,

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

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setCity($this);
        }

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

    /**
     * @return Collection<int, CityDiscount>
     */
    public function getCityDiscounts(): Collection
    {
        return $this->cityDiscounts;
    }

    public function addCityDiscount(CityDiscount $cityDiscount): static
    {
        if (!$this->cityDiscounts->contains($cityDiscount)) {
            $this->cityDiscounts->add($cityDiscount);
            $cityDiscount->setCity($this);
        }

        return $this;
    }

    public function removeCityDiscount(CityDiscount $cityDiscount): static
    {
        if ($this->cityDiscounts->removeElement($cityDiscount)) {
            // set the owning side to null (unless already changed)
            if ($cityDiscount->getCity() === $this) {
                $cityDiscount->setCity(null);
            }
        }

        return $this;
    }

    public function getTotalDiscount(): float
    {
        $totalDiscount = 0;
        foreach ($this->getCityDiscounts() as $cityDiscount){
            $totalDiscount += $cityDiscount->getDiscount() ?? 0;
        }
        return  $totalDiscount;
    }
}
