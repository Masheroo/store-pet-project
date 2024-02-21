<?php

namespace App\EventListener\EntityListener;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsEntityListener(event: Events::prePersist, method: 'computeAndSetSlug', entity: Category::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'computeAndSetSlug', entity: Category::class)]
readonly class CategoryListener
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function computeAndSetSlug(Category $category, PrePersistEventArgs|PreUpdateEventArgs $args): void
    {
        if ($category->getSlug()) {
            return;
        }

        $category->setSlug($this->slugger->slug($category->getName())->toString());
    }
}
