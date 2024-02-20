<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Lot::class)]
    private Collection $lots;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: CategoryField::class, orphanRemoval: true)]
    private Collection $fields;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    public function __construct(
        #[ORM\Column(length: 255)]
        private string $name
    ) {
        $this->lots = new ArrayCollection();
        $this->fields = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Collection<int, Lot>
     */
    public function getLots(): Collection
    {
        return $this->lots;
    }

    /**
     * @return Collection<int, CategoryField>
     */
    #[Ignore]
    public function getFields(): Collection
    {
        return $this->fields;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}
