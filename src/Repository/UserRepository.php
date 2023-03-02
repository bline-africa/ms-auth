<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface,UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }


    public function loadUserByUsername(string $usernameandrole): ?User
    {
        $entityManager = $this->getEntityManager();

        $userRepository = $entityManager->getRepository(User::class);
        list($username, $role) = explode(':', $usernameandrole);
        $user = $userRepository->findOneBy([
            'username' => $username,
            'role' => $role
        ]);

        if (!$user) {
            throw new UsernameNotFoundException('User not found');
        }

        return $user;
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

    public function findByType($role = "ROLE_PROVIDER", $id = 1)
    {
        $is_deleted = true;
        return $query = $this->getEntityManager()
            ->getConnection()
            ->executeQuery(
                <<<'SQL'
            SELECT id,lastname,firstname,username,created_at,email,isvalid,is_kyc_check,account_type,title,fax,company_name,address,phone1,phone2,last_connect,title,fax,tva,is_deleted,state FROM public.user
            WHERE profil_id_id  = :id
             
            ORDER BY created_at desc
SQL,
                ['id' => $id]
            )->fetchAllAssociative();
    }

    public function findUuidByType($role = "ROLE_PROVIDER", $id = 1)
    {
        $isdeleted = true;
        return $query = $this->getEntityManager()
            ->getConnection()
            ->executeQuery(
                <<<'SQL'
            SELECT id FROM public.user
            WHERE profil_id_id  = :id
           
            ORDER BY created_at desc
SQL,
                ['id' => $id]
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

    public function listUserByRange($nb,$profilId)
    {
        $falseVal = true;
       // $nullVal = "null";
       
        return $this->createQueryBuilder('n')
           ->where('n.state is not null')
           ->andWhere('n.state = :val2')
           ->andWhere('n.profilId = :val3')
            ->setParameter('val2', $falseVal)
            ->setParameter('val3', $profilId)
            ->orderBy('n.id', 'ASC')
             ->setMaxResults($nb)
            ->getQuery()
            ->getResult();
    }
}
