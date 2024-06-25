<?php

namespace App\Serializer\Normalizer;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UploadFileDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        return $data;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        return UploadedFile::class === $type;
    }
}
