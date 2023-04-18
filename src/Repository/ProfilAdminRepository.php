<?php

namespace App\Repository;

use App\Entity\ProfilAdmin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProfilAdmin|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProfilAdmin|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProfilAdmin[]    findAll()
 * @method ProfilAdmin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfilAdminRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProfilAdmin::class);
    }

    // /**
    //  * @return ProfilAdmin[] Returns an array of ProfilAdmin objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProfilAdmin
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
