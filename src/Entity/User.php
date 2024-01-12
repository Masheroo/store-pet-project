<?php

namespace App\Entity;

use App\Entity\Discount\UserDiscount;
use App\Exceptions\LackOfBalanceException;
use App\Exceptions\OutOfLotCountException;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('email', message: 'This Email is already used.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLES = [
        'admin' => self::ROLE_ADMIN,
        'manager' => self::ROLE_MANAGER,
        'user' => self::ROLE_USER,
    ];
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    /** @var string */
    public const ROLE_USER = 'ROLE_USER';

    public const PASSWORD_MIN_LENGTH = 6;
    public const ROLE_MANAGER = 'ROLE_MANAGER';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email]
    #[Assert\NotBlank]
    private ?string $email = null;

    /** @var array<array-key, string> $roles */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    #[Assert\NotBlank]
    private string $password = '';

    #[ORM\Column]
    private float $balance = 0;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: true)]
    private ?City $city = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserDiscount::class)]
    private Collection $userDiscounts;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Order::class)]
    private Collection $orders;

    public function __construct()
    {
        $this->userDiscounts = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /** @return array<array-key, string> */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = static::ROLE_USER;

        return array_unique($roles);
    }

    /** @param array<array-key, string> $roles */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive dat    a on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function replenishBalance(float $amount): void
    {
        $this->balance += $amount;
    }

    /**
     * @throws LackOfBalanceException
     */
    private function subtractionFromBalance(float $amount): void
    {
        if ($this->balance < $amount) {
            throw new LackOfBalanceException(sprintf('There is not enough balance to buy. Your balance: %s. Require: %s', $this->balance, $amount));
        }
        $this->balance -= $amount;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getDiscounts(): Collection
    {
        return $this->userDiscounts;
    }

    /**
     * @throws OutOfLotCountException
     * @throws LackOfBalanceException
     */
    public function payOrder(Order $order): void
    {
        if ($order->getLot()->getCount() < $order->getQuantity()) {
            throw new OutOfLotCountException('Quantity to purchase greater than lot count');
        }

        $this->subtractionFromBalance($order->getPayPrice());
    }
}
