<?php

namespace App\Repository;

use App\Entity\InfoUser;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InfoUser>
 *
 * @method InfoUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfoUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfoUser[]    findAll()
 * @method InfoUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InfoUser::class);
    }

    public function getUserInfoByID( $user_id )
    {
        $query = $this->createQueryBuilder("p")
        ->where('p.user = :w')
        ->setParameter(':w', $user_id);

        return $query->getQuery()->getOneOrNullResult(); // on renvoie le résultat
     }

    public function InfobyId($id) {
        $qb = $this->createQueryBuilder('p')
                ->select('p, d') // Entity Utilisateur
                ->join('p.user', 'd')
                ->where('p.id = :id')
                ->setParameter('id', $id);

        return $qb->getQuery()->getResult();      
    }

    /**
    * @return InfoUser[] Returns an array of Itineraires objects
    */
   public function findInfoById(int $id): array
    {
        $conn = $this->getEntityManager();

        $query = $conn->createQuery(
            'SELECT p
            FROM App\Entity\InfoUser p
            WHERE p.user = :id'
        )->setParameter('id', $id);

        // returns an array of arrays (i.e. a raw data set)
        return $query->getResult();
    }

    /**
    * @return InfoUser[] Returns an array of Itineraires objects
    */

    public function findInfoByUser($id)
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
        return $result->fetch();
    }

    /**
    * @return InfoUser[] Returns an array of Itineraires objects
    */
       public function findInfoUser(int $id)
        {
            $conn = $this->getEntityManager();

            $query = $conn->createQuery(
                'SELECT p
                FROM App\Entity\InfoUser p
                WHERE p.id = :id'
            )->setParameter('id', $id)
            ->setMaxResults(1);

            // returns an array of arrays (i.e. a raw data set)
            return $infoUser = $query->setMaxResults(1)->getOneOrNullResult();
        }

   // /**
   //  * @return InfoUser[] Returns an array of InfoUser objects
   //  */

   // /**
   //  * @return Utilisateur[] Returns an array of Utilisateur objects
   //  */
   // public function findById(int $id): array
   //  {
   //      $conn = $this->getEntityManager()->getConnection();
   //      $query = 
   //          'SELECT *
   //          FROM utilisateur p
   //          JOIN info_user i ON p.id = i.user_id
   //          WHERE i.user_id = :id';
   //      $result = $conn->executeQuery($query, ['id' => $id]);

   //      // returns an array of Product objects
   //      return $result->fetch();
   //  }
}
