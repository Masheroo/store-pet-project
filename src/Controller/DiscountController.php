<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\User;
use App\Request\Discount\CreateDiscountRequest;
use App\Request\Discount\CreateVolumeDiscountRequest;
use App\Service\Discount\DiscountService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/discount')]
class DiscountController extends AbstractController
{
    #[IsGranted(User::ROLE_ADMIN)]
    #[Route('/volume', name: 'create_volume_discount', methods: 'POST')]
    public function index(
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
}
