<?php

namespace App\Request;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\Image;

class UpdateLotRequest
{
    public ?string $title = null;

    public ?float $cost = null;

    public ?int $count = null;

    #[Image]
    public ?UploadedFile $image = null;
}