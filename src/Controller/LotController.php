<?php

namespace App\Controller;

use App\Entity\Lot;
use App\Repository\LotRepository;
use App\Request\CreateLotRequest;
use App\Service\Lot\LotService;
use App\Service\Resolver\RequestPayloadValueResolver;
use League\Flysystem\FilesystemException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
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

    /**
     * @throws FilesystemException
     */
    #[Route('/lot', name: 'create_lot', methods: ['POST'])]
    public function createLot(
        #[MapRequestPayload(resolver: RequestPayloadValueResolver::class)]
        CreateLotRequest $request,
        LotService $lotService,
    ): JsonResponse {

        $lotService->createLotFromRequest($request);

        return $this->json('');
    }
}
