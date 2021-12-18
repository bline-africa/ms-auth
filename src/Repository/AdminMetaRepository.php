<?php

namespace App\Repository;

use App\Entity\AdminMeta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AdminMeta|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdminMeta|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdminMeta[]    findAll()
 * @method AdminMeta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminMetaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminMeta::class);
    }

    // /**
    //  * @return AdminMeta[] Returns an array of AdminMeta objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AdminMeta
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
