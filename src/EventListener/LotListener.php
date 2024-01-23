<?php

namespace App\EventListener;

use App\Controller\ExternalApiController;
use App\Entity\Lot;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;

#[AsEntityListener(event: Events::preFlush, method: 'clearCache', entity: Lot::class)]
class LotListener
{
    public function __construct(
        private readonly CacheInterface $cache
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function clearCache(Lot $lot, PreFlushEventArgs $args): void
    {
        $this->cache->delete(ExternalApiController::ALL_LOTS_CACHE_KEY);
    }
}