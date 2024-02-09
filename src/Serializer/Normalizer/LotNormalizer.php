<?php

namespace App\Serializer\Normalizer;

use App\Entity\Lot;
use App\Service\Manager\LocalImageManager;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @method array getSupportedTypes(?string $format)
 */
class LotNormalizer implements NormalizerInterface
{
    public function __construct(
        private readonly LocalImageManager $imageManager
    ) {}
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
            $data['image'] = $this->imageManager->getPublicLink($object->getImage());
        }catch (FileNotFoundException){
            $data['image'] = null;
        }

        try {
            $data['preview'] = $this->imageManager->getPublicLink($object->getPreview());
        }catch (FileNotFoundException){
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