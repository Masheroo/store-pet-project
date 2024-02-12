<?php

namespace App\Serializer\Normalizer;

use App\Entity\Lot;
use App\Service\Manager\FileManager;
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

    public function normalize(mixed $object, string $format = null, array $context = []): float|int|bool|\ArrayObject|array|string|null
    {
        $data = [];
        /** @var Lot $object */

        $data['id'] = $object->getId();
        $data['title'] = $object->getTitle();
        $data['cost'] = $object->getCost();
        $data['count'] = $object->getCount();
        $data['image'] = $object->getImage() ? $this->imageManager->getPublicLink($object->getImage()) : null;
        $data['preview'] = $object->getPreview() ? $this->imageManager->getPublicLink($object->getPreview()) : null;

        return $data;
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof Lot;
    }
}
