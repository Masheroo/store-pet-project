<?php

namespace App\Controller;

use App\Repository\CityRepository;
use App\Repository\UserRepository;
use App\Request\RegistrationRequest;
use App\Service\UserService;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class AuthController extends AbstractController
{
    #[Route('/registration', name: 'app_auth', methods: ['POST'])]
    public function index(
        #[MapRequestPayload] RegistrationRequest $request,
        UserService $userService,
        UserRepository $userRepository,
        CityRepository $cityRepository,
        AuthenticationSuccessHandler $successHandler
    ): Response {
        $city = $cityRepository->find($request->city);
        $user = $userService->createUser($request->email, $request->password, $city);

        $userRepository->persistAndFlush($user);

        return $successHandler->handleAuthenticationSuccess($user);
    }
}
