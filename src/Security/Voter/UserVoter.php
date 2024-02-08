<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Repository\AccessRightRepository;
use App\Security\AccessValue;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserVoter extends Voter
{
    public function __construct(
        private readonly Security $security,
        private readonly AccessRightRepository $accessRightRepository
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [AccessValue::AddUserDiscount->value, AccessValue::ReplenishUserBalance->value]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();
        if (!$user instanceof User && !$this->security->isGranted(User::ROLE_MANAGER)) {
            throw new AccessDeniedException('You must have role Manager or higher');
        }
        if ($this->security->isGranted(User::ROLE_ADMIN)) {
            return true;
        }

        return match ($attribute) {
            AccessValue::ReplenishUserBalance->value => $this->canReplenishUserBalance($user),
            AccessValue::AddUserDiscount->value => $this->canAddUserDiscount($user),
        };
    }

    private function canReplenishUserBalance(User $user): bool
    {
        $accessRight = $this->accessRightRepository->findBy(['user' => $user, 'value' => AccessValue::ReplenishUserBalance]);

        return (bool) $accessRight;
    }

    private function canAddUserDiscount(User $user): bool
    {
        $accessRight = $this->accessRightRepository->findBy(['user' => $user, 'value' => AccessValue::AddUserDiscount]);

        return (bool) $accessRight;
    }
}
