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

    public function flush(): void
    {
        $this->getEntityManager()->flush();
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

    /**
     * @param ?array $filters {filter:{valueId: int}}
     */
    public function findByFilters(?array $filters, \App\Entity\Category $category): array
    {
        $query = $this->createQueryBuilder('lot')
            ->where('lot.category = :category')
            ->setParameter('category', $category);

        if ($filters || empty($filter)) {
            foreach ($filters as $index => $filter) {
                $query->join('lot.fieldValues', $currentJoin = 'lfv'.$index)
                    ->andWhere(sprintf('%s.id in (:filter%s)', $currentJoin, $index))
                    ->setParameter('filter'.$index, $filter);
            }
        }

        return $query->getQuery()->getResult();
    }
}
