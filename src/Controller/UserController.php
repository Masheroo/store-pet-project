<?php

namespace App\Controller;

use App\Entity\User;
use App\Exceptions\RoleDoesNotExistsException;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use App\Request\AddAccessRightRequest;
use App\Request\ChangeUserPasswordRequest;
use App\Request\CreateExternalLotRequest;
use App\Request\ReplenishRequest;
use App\Security\AccessValue;
use App\Service\AccessService;
use App\Service\ExternalApiToken\ExternalApiTokenService;
use App\Service\UserService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
class UserController extends AbstractController
{
    #[IsGranted(User::ROLE_ADMIN)]
    #[Route('/user/{id}/access', name: 'user_add_access_right', methods: ['POST'])]
    public function addAccessRight(
        #[MapRequestPayload]AddAccessRightRequest $request,
        User $user,
        AccessService $accessService
    ): JsonResponse {
        $accessService->createAndAddAccessRightToManagerUser($user, $request->accessValue);
        return $this->json('Access right has been added');
    }

    #[IsGranted(AccessValue::ReplenishUserBalance->value)]
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

    /**
     * @throws RoleDoesNotExistsException
     */
    #[IsGranted(User::ROLE_ADMIN)]
    #[Route('/user/{id}/role/{role}', name: 'switch_user_to_admin', methods: ['POST'])]
    public function switchUserRole(User $user, string $role, UserRepository $userRepository): JsonResponse
    {
        if (!array_key_exists($role, User::ROLES)) {
            throw new RoleDoesNotExistsException(
                sprintf('Role %s does not exists. Available roles: %s', $role, implode(', ', array_keys(User::ROLES)))
            );
        }

        $user->setRoles([User::ROLES[$role]]);
        $userRepository->flush();

        return $this->json('Success');
    }

    #[Route('/user/shopping-list', 'get_shopping_list', methods: ['GET'])]
    public function getShoppingList(OrderRepository $orderRepository): JsonResponse
    {
        return $this->json($orderRepository->findBy(['user' => $this->getUser()]));
    }

    #[IsGranted(User::ROLE_ADMIN)]
    #[Route('/token', name: 'create_external_token', methods: ['POST'])]
    public function createToken(
        #[MapRequestPayload] CreateExternalLotRequest $request,
        ExternalApiTokenService $tokenService
    ): JsonResponse {
        $token = $tokenService->create($request->token_name);
        $body = [
            'token' => $token->getToken(),
            'token_name' => $token->getName(),
        ];

        return $this->json($body);
    }

    #[IsGranted(User::ROLE_ADMIN)]
    #[Route('/user/{id}/change-password', name: 'user_change_password', methods: ['POST'])]
    public function changeUserPassword(
        #[MapRequestPayload] ChangeUserPasswordRequest $request,
        User $user,
        UserService $service,
        JWTTokenManagerInterface $JWTTokenManager,
    ): JsonResponse {
        $service->changePassword($user, $request->password);
        $JWTTokenManager->create($user);

        return $this->json('Password changed successful!');
    }
}
