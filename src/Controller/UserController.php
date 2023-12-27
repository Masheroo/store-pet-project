<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Request\ReplenishRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
class UserController extends AbstractController
{
    #[IsGranted(User::ROLE_MANAGER)]
    #[Route('/user/{id}', name: 'user_replenish_balance', methods: ['POST'])]
    public function replenishBalance(
        User $user,
        #[MapRequestPayload] ReplenishRequest $request,
        UserRepository $userRepository
    ): JsonResponse {
        $user->replenishBalance($request->amount);
        $userRepository->flush();

        return $this->json([]);
    }

    #[IsGranted(User::ROLE_MANAGER)]
    #[Route('/users', name: 'get_all_users', methods: ['GET'])]
    public function getAllUsers(UserRepository $userRepository): JsonResponse
    {
        return $this->json($userRepository->findAll());
    }

    #[IsGranted(User::ROLE_ADMIN)]
    #[Route('/user/{id}/switch', name: 'switch_user_to_admin', methods: ['POST'])]
    public function switchUserToAdmin(User $user, UserRepository $userRepository): JsonResponse
    {
        $user->setRoles([User::ROLE_ADMIN]);
        $userRepository->flush();

        return $this->json('Success');
    }
}
