<?php

namespace App\Controller;

use App\Repository\LotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/external/api')]
class ExternalApiController extends AbstractController
{
    #[Route('/lots', name: 'app_external_api', methods: ['GET'])]
    public function getAllLots(LotRepository $repository): JsonResponse
    {
        return $this->json($repository->findAll());
    }
}
