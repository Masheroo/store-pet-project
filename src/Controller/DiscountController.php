<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Lot;
use App\Entity\User;
use App\Request\Discount\CreateDiscountRequest;
use App\Request\Discount\CreateLotDiscountRequest;
use App\Request\Discount\CreateUserDiscountRequest;
use App\Request\Discount\CreateVolumeDiscountRequest;
use App\Security\AccessValue;
use App\Service\Discount\DiscountService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(User::ROLE_MANAGER)]
#[Route('/api/discount')]
class DiscountController extends AbstractController
{
    #[IsGranted(User::ROLE_ADMIN)]
    #[Route('/volume', name: 'create_volume_discount', methods: 'POST')]
    public function createVolumeDiscount(
        #[MapRequestPayload]
        CreateVolumeDiscountRequest $request,
        DiscountService $discountService
    ): JsonResponse {
        return $this->json($discountService->createVolumeDiscount($request->amount, $request->discount));
    }

    #[IsGranted(User::ROLE_ADMIN)]
    #[Route('/city/{id}', name: 'create_city_discount', methods: 'POST')]
    public function createCityDiscount(
        City $city,
        #[MapRequestPayload] CreateDiscountRequest $request,
        DiscountService $discountService
    ): JsonResponse {
        return $this->json($discountService->createCityDiscount($city, $request->discount));
    }

    #[IsGranted(AccessValue::AddUserDiscount->value)]
    #[Route('/user/{id}', name: 'create_user_discount', methods: 'POST')]
    public function createUserDiscount(
        User $user,
        #[MapRequestPayload] CreateUserDiscountRequest $request,
        DiscountService $discountService
    ): JsonResponse {
        return $this->json($discountService->createUserDiscount($user, $request->discount, $request->type));
    }

    #[Route('/lot/{id}', name: 'create_lot_discount', methods: 'POST')]
    public function createLotDiscount(
        Lot $lot,
        #[MapRequestPayload] CreateLotDiscountRequest $request,
        DiscountService $discountService
    ): JsonResponse
    {
        return $this->json($discountService->createLotDiscount($lot, $request->discount, $request->countOfPurchases));
    }
}
