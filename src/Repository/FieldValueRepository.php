<?php

namespace App\Repository;

use App\Entity\FieldValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FieldValue>
 *
 * @method FieldValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method FieldValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method FieldValue[]    findAll()
 * @method FieldValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FieldValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FieldValue::class);
    }
    public function save(FieldValue $fieldValue): void
    {
        $em = $this->getEntityManager();
        $em->persist($fieldValue);
        $em->flush();
    }
}
