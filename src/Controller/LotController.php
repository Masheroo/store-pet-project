<?php

namespace App\Controller;

use App\Entity\Lot;
use App\Entity\Order;
use App\Entity\User;
use App\Message\NewLotEmailMessage;
use App\Repository\LotRepository;
use App\Repository\UserRepository;
use App\Request\BuyLotRequest;
use App\Request\CreateLotRequest;
use App\Request\UpdateLotRequest;
use App\Service\Lot\LotService;
use App\Service\Manager\LocalImageManager;
use App\Service\Resolver\RequestPayloadValueResolver;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\VarExporter\Exception\NotInstantiableTypeException;

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
    #[IsGranted('ROLE_MANAGER')]
    #[Route('/lot', name: 'create_lot', methods: ['POST'])]
    public function createLot(
        #[MapRequestPayload(resolver: RequestPayloadValueResolver::class)]
        CreateLotRequest $request,
        LotService $lotService,
        MessageBusInterface $messageBus,
        LocalImageManager $imageManager,
        UserRepository $userRepository
    ): JsonResponse {
        $lot = $lotService->createLotFromRequest($request);

        $users = $userRepository->findAll();
        foreach ($users as $user) {
            $messageBus->dispatch(
                new NewLotEmailMessage(
                    $lot->getId(),
                    $lot->getTitle(),
                    $lot->getCount(),
                    $lot->getCost(),
                    $imageManager->getPublicLink($lot->getImage()),
                    $user->getEmail()
                )
            );
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
        LotService $lotService
    ): JsonResponse {
        $updatedLot = $lotService->updateLotFromRequest($lot, $request);

        return $this->json($updatedLot);
    }

    /**
     * @throws NotInstantiableTypeException
     */
    #[Route('/lot/buy/{id}', name: 'buy_lot', methods: ['POST'])]
    public function buyLot(
        Lot $lot,
        #[MapRequestPayload] BuyLotRequest $request,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new NotInstantiableTypeException(gettype($user));
        }

        $order = new Order($user, $request->quantity, 0, $lot);

        $user->payOrder($order);
        $lot->setCount($lot->getCount() - $order->getQuantity());

        $entityManager->persist($order);
        $entityManager->flush();

        return $this->json('Lot has been purchased');
    }
}
