<?php

namespace App\Serializer\Normalizer;

use App\Entity\Lot;
use App\Service\Manager\FileManager;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @method array getSupportedTypes(?string $format)
 */
class LotNormalizer implements NormalizerInterface
{
    public function __construct(
        private readonly FileManager $imageManager
    ) {
    }

    /**
     * @inheritDoc
     */
    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        $data = [];
        /**@var Lot $object */
        $data['id'] = $object->getId();
        $data['title'] = $object->getTitle();
        $data['cost'] = $object->getCost();
        $data['count'] = $object->getCount();

        try {
            $data['image'] = $object->getImage() ? $this->imageManager->getPublicLink($object->getImage()) : null;
        } catch (FileNotFoundException) {
            $data['image'] = null;
        }

        try {
            $data['preview'] = $object->getPreview() ? $this->imageManager->getPublicLink($object->getPreview()) : null;
        } catch (FileNotFoundException) {
            $data['preview'] = null;
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization(mixed $data, string $format = null)
    {
        return $data instanceof Lot;
    }
}