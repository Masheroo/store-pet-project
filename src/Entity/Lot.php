<?php

namespace App\Entity;

use App\Entity\Discount\LotDiscount;
use App\Exceptions\LotCountException;
use App\Repository\LotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

#[ORM\Entity(repositoryClass: LotRepository::class)]
#[HasLifecycleCallbacks]
class Lot
{
    public const PREVIEW_SIZE = 200;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\OneToMany(mappedBy: 'lot', targetEntity: LotDiscount::class)]
    private Collection $lotDiscounts;

    #[ORM\OneToMany(mappedBy: 'lot', targetEntity: Order::class)]
    private Collection $orders;

    #[ORM\ManyToMany(targetEntity: FieldValue::class)]
    private Collection $fieldValues;

    public function __construct(
        #[ORM\Column(length: 255)]
        private string $title,

        #[ORM\Column]
        private float $cost,

        #[ORM\Column]
        private int $count,

        #[ORM\Column(length: 255, nullable: true)]
        private string $image,

        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        private readonly User $owner,

        #[ORM\ManyToOne(inversedBy: 'lots')]
        #[ORM\JoinColumn(nullable: false)]
        private readonly Category $category,

        #[ORM\Column(length: 255, nullable: false)]
        private ?string $preview = null
    ) {
        $this->lotDiscounts = new ArrayCollection();
        $this->fieldValues = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCost(): float
    {
        return $this->cost;
    }

    public function setCost(float $cost): static
    {
        $this->cost = $cost;

        return $this;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;

        return $this;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @throws LotCountException
     */
    public function decreaseCount(int $countToDecrease): void
    {
        if ($countToDecrease > $this->count) {
            throw new LotCountException('Quantity to decrease more than count in lot.');
        }
        $this->count -= $countToDecrease;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function getPreview(): ?string
    {
        return $this->preview;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function getFieldValues(): Collection
    {
        return $this->fieldValues;
    }

    public function addFieldValue(FieldValue $fieldValue): void
    {
        if ($fieldValue->getField()->getCategory() !== $this->category) {
            throw new \DomainException('Данное значение не относится к полям категории этого лота');
        }

        if ($this->fieldValues->contains($fieldValue)) {
            return;
        }

        $this->fieldValues->add($fieldValue);
    }

    public function removeFieldValue(FieldValue $fieldValue): void
    {
        if ($fieldValue->getField()->getCategory() !== $this->category) {
            throw new \DomainException('Данное значение не относится к полям категории этого лота');
        }

        if ($this->fieldValues->contains($fieldValue)) {
           $this->fieldValues->remove($fieldValue);
        }
    }
}
