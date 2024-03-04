<?php

namespace App\EventListener\EntityListener;

use App\Entity\CategoryField;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsEntityListener(event: Events::prePersist, method: 'computeAndSetSlug', entity: CategoryField::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'computeAndSetSlug', entity: CategoryField::class)]
readonly class CategoryFieldListener
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function computeAndSetSlug(CategoryField $field, PrePersistEventArgs|PreUpdateEventArgs $args): void
    {
        if ($field->getSlug()) {
            return;
        }

        $field->setSlug($this->slugger->slug($field->getName())->toString());
    }
}
