<?php

namespace App\Repository;

use App\Entity\CategorieUserMeta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CategorieUserMeta|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategorieUserMeta|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategorieUserMeta[]    findAll()
 * @method CategorieUserMeta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieUserMetaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorieUserMeta::class);
    }

    // /**
    //  * @return CategorieUserMeta[] Returns an array of CategorieUserMeta objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CategorieUserMeta
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
