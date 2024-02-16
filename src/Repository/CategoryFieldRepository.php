<?php

namespace App\Repository;

use App\Entity\CategoryField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CategoryField>
 *
 * @method CategoryField|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryField|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryField[]    findAll()
 * @method CategoryField[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryFieldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryField::class);
    }
}
