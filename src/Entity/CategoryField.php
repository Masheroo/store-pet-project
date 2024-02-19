<?php

namespace App\Entity;

use App\Repository\CategoryFieldRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryFieldRepository::class)]
class CategoryField
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function __construct(
        #[ORM\Column(length: 255)]
        private string $name,

        #[ORM\ManyToOne(inversedBy: 'fields')]
        #[ORM\JoinColumn(nullable: false)]
        private readonly Category $category,
    ) {
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

    public function getCategory(): Category
    {
        return $this->category;
    }
}
