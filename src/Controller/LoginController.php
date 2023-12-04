<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class LoginController extends AbstractController
{
    #[Route('/login')]
    public function login()
    {
        return $this->json('login');
    }

    #[Route('/test')]
    public function test(): JsonResponse
    {
        return $this->json('test');
    }
}
