<?php

namespace App\Repository;

use App\Entity\Discount\VolumeDiscount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VolumeDiscount>
 *
 * @method VolumeDiscount|null find($id, $lockMode = null, $lockVersion = null)
 * @method VolumeDiscount|null findOneBy(array $criteria, array $orderBy = null)
 * @method VolumeDiscount[]    findAll()
 * @method VolumeDiscount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VolumeDiscountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VolumeDiscount::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findBiggestDiscountByAmount(float $amount): ?VolumeDiscount
    {
        $queryBuilder = $this->createQueryBuilder('discount')
            ->where('discount.amount < :amount')
            ->setParameter('amount', $amount)
            ->orderBy('discount.discount', 'DESC');

        $query = $queryBuilder->getQuery();

        $result = $query->setMaxResults(1)->getOneOrNullResult();

        return $result instanceof VolumeDiscount ? $result : null;
    }
}
