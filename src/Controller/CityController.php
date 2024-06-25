<?php

namespace App\Controller;

use App\Repository\CityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class CityController extends AbstractController
{
    #[Route('/cities', name: 'get_all_cities', methods: ['GET'])]
    public function getAll(CityRepository $cityRepository): JsonResponse
    {
        return $this->json($cityRepository->findAll());
    }
}
