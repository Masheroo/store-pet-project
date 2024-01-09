<?php

namespace App\Repository;

use App\Entity\UserDiscount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserDiscount>
 *
 * @method UserDiscount|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserDiscount|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserDiscount[]    findAll()
 * @method UserDiscount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserDiscountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserDiscount::class);
    }

//    /**
//     * @return UserDiscount[] Returns an array of UserDiscount objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserDiscount
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
