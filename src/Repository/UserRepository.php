<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findByType($role = "ROLE_PROVIDER")
    {
$is_deleted = true;
        return $query = $this->getEntityManager()
            ->getConnection()
            ->executeQuery(
                <<<'SQL'
            SELECT id,lastname,firstname,username,email,isvalid,is_kyc_check,account_type,title,fax,company_name,address,phone1,phone2,last_connect,title,fax,tva,is_deleted FROM public.user
            WHERE roles  :role
             
            ORDER BY created_at desc
SQL,
                ['role' => $role]
            )->fetchAllAssociative();
    }

    public function findUuidByType($role = "ROLE_PROVIDER")
    {
        $isdeleted = true;
        return $query = $this->getEntityManager()
            ->getConnection()
            ->executeQuery(
                <<<'SQL'
            SELECT id FROM public.user
            WHERE roles::jsonb ?? :role
           
            ORDER BY created_at desc
SQL,
                ['role' => $role]
            )->fetchAllAssociative();
    }

    public function deleteAllUsers()
    {
        $query = $this->createQueryBuilder('e')
            ->delete()
            ->getQuery()
            ->execute();
        return $query;
    }
}
