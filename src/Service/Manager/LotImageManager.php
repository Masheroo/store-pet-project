<?php

namespace App\Service\Manager;

use App\Entity\Lot;
use Imagine\Image\ImagineInterface;
use League\Flysystem\FilesystemException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class LotImageManager
{
    public function __construct(
      private FileManager $fileManager,
      private ImagineInterface $imagine
    ) {
    }

    /**
     * Returns filename
     * @param UploadedFile $image
     * @return string
     * @throws FilesystemException
     */
    public function convertUploadedImageToPreviewAndSave(UploadedFile $image): string
    {
        $openedImage = $this->imagine->open($image->getRealPath());
        $imageSize = $openedImage->getSize();

        if ($imageSize->getHeight() > Lot::PREVIEW_SIZE || $imageSize->getHeight() > Lot::PREVIEW_SIZE) {
            if ($imageSize->getWidth() > $imageSize->getHeight()) {
                $newImageSize = $imageSize->widen(Lot::PREVIEW_SIZE);
            } else {
                $newImageSize = $imageSize->heighten(Lot::PREVIEW_SIZE);
            }

            $openedImage->resize($newImageSize);
        }

        $newFilename = uniqid() . '.' . $image->getClientOriginalExtension();
        $this->fileManager->write($newFilename, $openedImage->get($image->getClientOriginalExtension()));

        return $newFilename;
    }

}