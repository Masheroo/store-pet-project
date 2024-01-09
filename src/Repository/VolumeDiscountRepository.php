<?php

namespace App\Repository;

use App\Entity\Discount\VolumeDiscount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
}
