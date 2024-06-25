<?php

namespace App\Service;

use App\Entity\AccessRight;
use App\Entity\User;
use App\Repository\AccessRightRepository;
use App\Security\AccessValue;

class AccessService
{
    public function __construct(
        private readonly AccessRightRepository $accessRightRepository
    )
    {
    }

    public function createAndAddAccessRightToManagerUser(User $user, AccessValue $accessValue): void
    {
        if (!in_array(User::ROLE_MANAGER, $user->getRoles())){
            throw new \DomainException(sprintf('User %s is not a Manager', $user->getEmail()));
        }

        if ($this->accessRightRepository->findOneBy(['user' => $user, 'value' => $accessValue])){
            return;
        }

        $accessRight = new AccessRight($user, $accessValue);
        $this->accessRightRepository->persist($accessRight);
        $this->accessRightRepository->flush();
    }
}