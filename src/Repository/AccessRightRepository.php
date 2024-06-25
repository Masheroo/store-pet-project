<?php

namespace App\Repository;

use App\Entity\AccessRight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AccessRight>
 *
 * @method AccessRight|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccessRight|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccessRight[]    findAll()
 * @method AccessRight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccessRightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessRight::class);
    }

    public function persist(AccessRight $accessRight): void
    {
        $this->getEntityManager()->persist($accessRight);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
