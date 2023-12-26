<?php

namespace App\Controller;

use App\Entity\Lot;
use App\Notifier\EmailNotifier;
use App\Repository\LotRepository;
use App\Repository\UserRepository;
use App\Request\CreateLotRequest;
use App\Request\UpdateLotRequest;
use App\Service\Lot\LotService;
use App\Service\Resolver\RequestPayloadValueResolver;
use League\Flysystem\FilesystemException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
     * @throws TransportExceptionInterface
     */
    #[IsGranted('ROLE_MANAGER')]
    #[Route('/lot', name: 'create_lot', methods: ['POST'])]
    public function createLot(
        #[MapRequestPayload(resolver: RequestPayloadValueResolver::class)]
        CreateLotRequest $request,
        LotService $lotService,
        EmailNotifier $notifier,
        UserRepository $userRepository
    ): JsonResponse {
        $lot = $lotService->createLotFromRequest($request);

        $users = $userRepository->findAll();
        foreach ($users as $user) {
            $notifier->sendEmailAboutNewLot($lot, $user->getEmail());
        }

        return $this->json($lot);
    }

    /**
     * @throws FilesystemException
     */
    #[IsGranted('ROLE_MANAGER')]
    #[Route('/lot/{id}', name: 'delete_lot', methods: ['DELETE'])]
    public function delete(Lot $lot, LotService $lotService): JsonResponse
    {
        $lotService->deleteWithImage($lot);

        return $this->json([]);
    }

    #[IsGranted('ROLE_MANAGER')]
    #[Route('/lot/{id}', name: 'update_lot', methods: ['POST'])]
    public function update(
        #[MapRequestPayload(resolver: RequestPayloadValueResolver::class)]
        UpdateLotRequest $request,
        Lot $lot,
        LotService $lotService): JsonResponse
    {
        $updatedLot = $lotService->updateLotFromRequest($lot, $request);

        return $this->json($updatedLot);
    }
}
