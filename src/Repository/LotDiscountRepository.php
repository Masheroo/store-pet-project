<?php

namespace App\Repository;

use App\Entity\Discount\LotDiscount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LotDiscount>
 *
 * @method LotDiscount|null find($id, $lockMode = null, $lockVersion = null)
 * @method LotDiscount|null findOneBy(array $criteria, array $orderBy = null)
 * @method LotDiscount[]    findAll()
 * @method LotDiscount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LotDiscountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LotDiscount::class);
    }
}
