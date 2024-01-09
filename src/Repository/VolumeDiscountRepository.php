<?php

namespace App\Repository;

use App\Entity\VolumeDiscount;
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

//    /**
//     * @return VolumeDiscount[] Returns an array of VolumeDiscount objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?VolumeDiscount
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function persist(VolumeDiscount $volumeDiscount): void
    {
        $this->getEntityManager()->persist($volumeDiscount);
    }
    public function persistAndFlush(VolumeDiscount $volumeDiscount): void
    {
        $this->persist($volumeDiscount);
        $this->getEntityManager()->flush();
    }
}
