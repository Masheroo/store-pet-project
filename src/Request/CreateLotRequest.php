<?php

namespace App\Request;

use App\Entity\Category;
use Happyr\Validator\Constraint\EntityExist;
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

    #[EntityExist(entity: Category::class, property: 'id')]
    public ?int $category = null;
}
