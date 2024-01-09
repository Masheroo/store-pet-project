<?php

namespace App\Repository;

use App\Entity\CityDiscount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CityDiscount>
 *
 * @method CityDiscount|null find($id, $lockMode = null, $lockVersion = null)
 * @method CityDiscount|null findOneBy(array $criteria, array $orderBy = null)
 * @method CityDiscount[]    findAll()
 * @method CityDiscount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityDiscountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CityDiscount::class);
    }
}
