<?php

namespace App\Request;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class CreateLotRequest
{
    #[Assert\NotBlank]
    public string $title;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public float $cost;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public int $count;

    #[Assert\NotBlank]
    #[Assert\Image]
    public UploadedFile $image;
}
