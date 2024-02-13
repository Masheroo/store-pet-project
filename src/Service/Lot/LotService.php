<?php

namespace App\Service\Lot;

use App\Entity\Lot;
use App\Entity\User;
use App\Repository\LotRepository;
use App\Request\CreateLotRequest;
use App\Request\UpdateLotRequest;
use App\Service\Manager\FileManager;
use App\Service\Manager\LotImageManager;
use League\Flysystem\FilesystemException;

readonly class LotService
{
    public function __construct(
        private LotImageManager $lotImageManager,
        private readonly FileManager $fileManager,
        private LotRepository $repository
    ) {
    }

    /**
     * @throws FilesystemException
     */
    public function createLotFromRequest(CreateLotRequest $request, User $user): Lot
    {
        $lot = new Lot(
            $request->title,
            cost: $request->cost,
            count: $request->count,
            image: $this->fileManager->saveUploadedImage($request->image),
            owner: $user,
//            category: $request->category,
            preview: $this->lotImageManager->convertUploadedImageToPreviewAndSave($request->image)
        );

        $this->repository->persistAndFlush($lot);

        return $lot;
    }

    /**
     * @throws FilesystemException
     */
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
            $this->fileManager->delete($lot->getImage());
            $lot->setImage($this->fileManager->saveUploadedImage($request->image));
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
        $this->fileManager->delete($lot->getImage());
    }
}
