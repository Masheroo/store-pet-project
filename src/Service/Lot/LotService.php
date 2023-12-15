<?php

namespace App\Service\Lot;

use App\Entity\Lot;
use App\Repository\LotRepository;
use App\Request\CreateLotRequest;
use App\Service\Manager\LocalImageManager;

class LotService
{
    public function __construct(
        private readonly LocalImageManager $imageManager,
        private readonly LotRepository $repository
    ) {
    }

    public function createLotFromRequest(CreateLotRequest $request): void
    {
        $lot = new Lot(
            null,
            $request->title,
            $request->cost,
            $request->count,
            $this->imageManager->saveUploadedImage($request->image)
        );

        $this->repository->persistAndFlush($lot);
    }
}
