<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace App\Repository;

use App\Entity\Lot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lot>
 *
 * @method Lot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lot[]    findAll()
 * @method Lot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lot::class);
    }

    public function persistAndFlush(Lot $lot): void
    {
        $this->persist($lot);
        $this->getEntityManager()->flush();
    }

    public function persist(Lot $lot): void
    {
        $this->getEntityManager()->persist($lot);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getFirst(): Lot
    {
        return $this->createQueryBuilder('lot')
            ->orderBy('lot.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function deleteAndFlush(Lot $lot): void
    {
        $this->getEntityManager()->remove($lot);
        $this->getEntityManager()->flush();
    }
}
