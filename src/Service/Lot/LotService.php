<?php

namespace App\Service\Lot;

use App\Entity\Lot;
use App\Repository\LotRepository;
use App\Request\CreateLotRequest;
use App\Request\UpdateLotRequest;
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
    public function createLotFromRequest(CreateLotRequest $request): Lot
    {
        $lot = new Lot(
            $request->title,
            $request->cost,
            $request->count,
            $this->imageManager->saveUploadedImage($request->image)
        );

        $this->repository->persistAndFlush($lot);

        return $lot;
    }

    public function updateLotFromRequest(Lot $lot,  UpdateLotRequest $request): Lot
    {
        if ($request->title){
            $lot->setTitle($request->title);
        }

        if ($request->count){
            $lot->setCount($request->count);
        }

        if ($request->cost){
            $lot->setCost($request->cost);
        }

        if ($request->image){
            $this->imageManager->deleteIfExists($lot->getImage());
            $lot->setImage($this->imageManager->saveUploadedImage($request->image));
        }

        $this->repository->flush();

        return $lot;
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
