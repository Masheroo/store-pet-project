<?php

namespace App\Controller;

use App\Entity\Lot;
use App\Entity\Order;
use App\Entity\User;
use App\Exceptions\LotCountException;
use App\Message\NewLotEmailMessage;
use App\Repository\LotRepository;
use App\Repository\UserRepository;
use App\Request\BuyLotRequest;
use App\Request\CreateLotRequest;
use App\Request\UpdateLotRequest;
use App\Service\Discount\DiscountService;
use App\Service\Discount\DiscountServiceInterface;
use App\Service\Lot\LotService;
use App\Service\Manager\LocalImageManager;
use App\Service\Resolver\RequestPayloadValueResolver;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use League\Flysystem\FilesystemException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Lock\LockFactory;
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
     * @throws NonUniqueResultException
     * @throws LotCountException
     */
    #[Route('/lot/buy/{lotId}', name: 'buy_lot', methods: ['POST'])]
    public function buyLot(
        int $lotId,
        #[MapRequestPayload] BuyLotRequest $request,
        EntityManagerInterface $entityManager,
        DiscountService $discountService,
        LotRepository $lotRepository,
        LockFactory $factory
    ): JsonResponse {
        $lock = $factory->createLock('buy-lot-'.$lotId);
        $lock->acquire(true);

        try {
            $user = $this->getUser();

            if (!$user instanceof User) {
                throw new UnexpectedTypeException(gettype($user), User::class);
            }

            $lot = $lotRepository->find($lotId) ?? throw new NotFoundHttpException(sprintf('Lot with id = %s not found', $lotId));

            $order = new Order($user, $lot, $request->quantity);

            $order->setDiscounts($discountService->computeAllDiscountsForOrder($order));

            $user->payOrder($order);
            $lot->decreaseCount($order->getQuantity());

            $entityManager->persist($order);
            $entityManager->flush();
        } finally {
            $lock->release();
        }

        return $this->json($order);
    }
}
