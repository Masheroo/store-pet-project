<?php

namespace App\Service\Discount;

use App\Entity\VolumeDiscount;
use App\Repository\VolumeDiscountRepository;
use App\Request\Discount\CreateVolumeDiscountRequest;

class DiscountService
{
    public function __construct(
        private readonly VolumeDiscountRepository $volumeDiscountRepository
    )
    {
    }

    public function createVolumeDiscountFromRequest(CreateVolumeDiscountRequest $request): VolumeDiscount
    {
        $volumeDiscount = new VolumeDiscount($request->amount, $request->discount);
        $this->volumeDiscountRepository->persistAndFlush($volumeDiscount);

        return $volumeDiscount;
    }
}