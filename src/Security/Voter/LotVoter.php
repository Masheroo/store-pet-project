<?php

namespace App\Security\Voter;

use App\Entity\Lot;
use App\Entity\User;
use App\Repository\AccessRightRepository;
use App\Security\AccessValue;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class LotVoter extends Voter
{
    public function __construct(
        private readonly AccessRightRepository $accessRightRepository,
        private readonly Security $security
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [
                AccessValue::UpdateOwnLot->value,
                AccessValue::CreateLot->value,
                AccessValue::DeleteOwnLot->value,
            ]) && ($subject instanceof Lot || null == $subject);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User && !$this->security->isGranted(User::ROLE_MANAGER)) {
            throw new AccessDeniedException('You must have role Manager or higher');
        }
        if ($this->security->isGranted(User::ROLE_ADMIN)) {
            return true;
        }
        /** @var ?Lot $lot */
        $lot = $subject;

        return match ($attribute) {
            AccessValue::CreateLot->value => $this->canAddLot($user),
            AccessValue::UpdateOwnLot->value => $this->canUpdateLot($user, $lot),
            AccessValue::DeleteOwnLot->value => $this->canDeleteLot($user, $lot),
        };
    }

    private function canAddLot(User $user): bool
    {
        $accessRight = $this->accessRightRepository->findBy(['user' => $user]) ;

        return (bool) $accessRight;
    }

    private function canUpdateLot(User $user, ?Lot $lot): bool
    {
        if (!$lot) {
            return false;
        }
        $accessRight = $this->accessRightRepository->findBy(['user' => $user, 'value' => AccessValue::UpdateOwnLot]);

        return $accessRight && $lot->getOwner()->getId() === $user->getId();
    }

    private function canDeleteLot(User $user, ?Lot $lot): bool
    {
        if (!$lot) {
            return false;
        }
        $accessRight = $this->accessRightRepository->findBy(['user' => $user, 'value' => AccessValue::DeleteOwnLot]);

        return $accessRight && $lot->getOwner()->getId() === $user->getId();
    }
}
