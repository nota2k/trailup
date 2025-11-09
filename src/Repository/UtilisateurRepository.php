<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Utilisateur>
 *
 * @implements PasswordUpgraderInterface<Utilisateur>
 *
 * @method Utilisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateur[]    findAll()
 * @method Utilisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateurRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Utilisateur) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function checkInfoUser($id): array
    {
       $qb = $this->createQueryBuilder('i')
            ->select('i, p')
            ->join('i.user', 'p.id')
            ->where('p.id = :user_id')
           ->setParameter('user_id', $id);
        $query = $qb->getQuery();
        return $query->execute();

    }

    public function findSingleUser($id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * 
            FROM info_user 
            JOIN utilisateur ON info_user.user_id
            WHERE utilisateur.id = info_user.user_id AND info_user.user_id = ?; 
            '
        ;
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $id);

        $result = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $result->fetchAssociative();
    }

    /**
    * @return Utilisateur[] Returns an array of Itineraires objects
    */
   public function findUser(int $id)
    {
        $conn = $this->getEntityManager();

        $query = $conn->createQuery(
            'SELECT p
            FROM App\Entity\Utilisateur p
            WHERE p.id = :id'
        )->setParameter('id', $id);

        // returns an array of arrays (i.e. a raw data set)
        return $user = $query->setMaxResults(1)->getOneOrNullResult();
    }

    // public function findItinerairesById($id)
    // {
    //     $conn = $this->getEntityManager()->getConnection();

    //     $sql = '
    //         SELECT * 
    //         FROM itineraires_utilisateur 
    //         JOIN itineraires ON itineraires.id = itineraires_utilisateur.itineraires_id;
    //         WHERE itineraires.id = itineraires_utilisateur.itineraires.id AND itineraires_utilisateur.utilisateur_id = ?; 
    //         '
    //     ;
    //     $stmt = $conn->prepare($sql);
    //     $stmt->bindValue(1, $id);

    //     $result = $stmt->executeQuery();

    //     // returns an array of arrays (i.e. a raw data set)
    //     return $result->fetchAssociative();
    // }

//    /**
//     * @return Utilisateur[] Returns an array of Utilisateur objects
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

//    public function findOneBySomeField($value): ?Utilisateur
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
