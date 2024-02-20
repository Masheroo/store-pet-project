<?php

namespace App\Controller;

use App\Entity\FieldValue;
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
use App\Security\AccessValue;
use App\Service\Discount\DiscountService;
use App\Service\Lot\LotService;
use App\Service\Manager\FileManager;
use App\Service\Resolver\RequestPayloadValueResolver;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemException;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Messenger\MessageBusInterface;
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
     */
    #[IsGranted(AccessValue::CreateLot->value)]
    #[Route('/lot', name: 'create_lot', methods: ['POST'])]
    public function createLot(
        #[MapRequestPayload(resolver: RequestPayloadValueResolver::class)]
        CreateLotRequest $request,
        LotService $lotService,
        MessageBusInterface $messageBus,
        FileManager $imageManager,
        UserRepository $userRepository
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();
        $lot = $lotService->createLotFromRequest($request, $user);

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
    #[IsGranted(AccessValue::DeleteOwnLot->value, 'lot')]
    #[Route('/lot/{id}', name: 'delete_lot', methods: ['DELETE'])]
    public function delete(Lot $lot, LotService $lotService): JsonResponse
    {
        $lotService->deleteWithImage($lot);

        return $this->json([]);
    }

    #[IsGranted(AccessValue::UpdateOwnLot->value, 'lot')]
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

    #[IsGranted(User::ROLE_MANAGER)]
    #[Route('/lot/{lot_id}/field/{field_id}/add', name: 'add_category_field_value_to_lot', methods: ['POST'])]
    public function addCategoryFieldValue(
        #[MapEntity(id: 'lot_id')]
        Lot $lot,
        #[MapEntity(id: 'field_id')]
        FieldValue $fieldValue,
        EntityManagerInterface $manager
    ): JsonResponse
    {
        $lot->addFieldValue($fieldValue);
        $manager->flush();

        return $this->json($lot->getFieldValues());
    }

    #[Route('/lot/{lot_id}/field/{field_id}/remove', name: 'remove_category_field_value_to_lot', methods: ['DELETE'])]
    public function removeCategoryFieldValue(
        #[MapEntity(id: 'lot_id')]
        Lot $lot,
        #[MapEntity(id: 'field_id')]
        FieldValue $fieldValue,
        EntityManagerInterface $manager
    ): JsonResponse
    {
        $lot->removeFieldValue($fieldValue);
        $manager->flush();

        return $this->json($lot->getFieldValues());
    }
}
