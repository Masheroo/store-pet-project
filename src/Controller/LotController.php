<?php

namespace App\Controller;

use App\Entity\Lot;
use App\Repository\LotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class LotController extends AbstractController
{
    #[Route('/lots', name: 'get_all_lots', methods: ['GET'])]
    public function getAllLots(LotRepository $repository): JsonResponse
    {
        return $this->json($repository->findAll());
    }

    #[Route('/lot/{id}', name: 'get_one_lot', methods: ['GET'])]
    public function getOneLot(Lot $lot): JsonResponse
    {
        return $this->json($lot);
    }
}
