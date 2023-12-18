<?php

namespace App\Service\Lot;

use App\Entity\Lot;
use App\Repository\LotRepository;
use App\Request\CreateLotRequest;
use App\Service\Manager\LocalImageManager;
use League\Flysystem\FilesystemException;

class LotService
{
    public function __construct(
        private readonly LocalImageManager $imageManager,
        private readonly LotRepository $repository
    ) {
    }

    /**
     * @throws FilesystemException
     */
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

    /**
     * @throws FilesystemException
     */
    public function deleteWithImage(Lot $lot): void
    {
        $this->repository->deleteAndFlush($lot);
        $this->imageManager->deleteIfExists($lot->getImage());
    }
}
