<?php

namespace App\Entity;

use App\Repository\AccessRightRepository;
use App\Security\AccessValue;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccessRightRepository::class)]
class AccessRight
{
    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'value')]
        private User $user,
        #[ORM\Column(type: 'string', length: 255, enumType: AccessValue::class)]
        private AccessValue $value,
    ) {
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getValue(): AccessValue
    {
        return $this->value;
    }

    public function setValue(AccessValue $value): void
    {
        $this->value = $value;
    }
}
