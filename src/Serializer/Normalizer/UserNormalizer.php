<?php

namespace App\Serializer\Normalizer;

use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @method array getSupportedTypes(?string $format)
 */
class UserNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        /* @var User $object */
        return [
            'id' => $object->getId(),
            'email' => $object->getEmail(),
            'city_id' => $object->getCity()?->getId(),
            'role' => array_search($object->getRoles()[0], User::ROLES),
            'balance' => $object->getBalance(),
        ];
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof User;
    }
}
