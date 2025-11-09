<?php

namespace App\Repository;

use App\Entity\Itineraires;
use App\Entity\Utilisateur;

use Doctrine\ORM\EntityManagerInterface;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
/**
 * @extends ServiceEntityRepository<Itineraires>
 *
 * @method Itineraires|null find($id, $lockMode = null, $lockVersion = null)
 * @method Itineraires|null findOneBy(array $criteria, array $orderBy = null)
 * @method Itineraires[]    findAll()
 * @method Itineraires[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItinerairesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Itineraires::class);
    }

   /**
    * @return Itineraires[] Returns an array of Itineraires objects
    */
   public function findItinerairesById($id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * 
            FROM itineraires_utilisateur 
            JOIN itineraires ON itineraires.id = itineraires_utilisateur.itineraires_id;
            WHERE itineraires.id = itineraires_utilisateur.itineraires.id AND itineraires_utilisateur.utilisateur_id = ?; 
            '
        ;
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $id);

        $result = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $result->fetchAssociative();
    }

    public function findAllItinerairesByUser(int $userId): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM itineraires i
            JOIN itineraires_utilisateur u ON i.id = u.itineraires_id
            JOIN utilisateur v ON u.utilisateur_id = v.id
            WHERE v.id = :user
            ';

        $resultSet = $conn->executeQuery($sql, ['user' => $userId]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function findCreateurInfosById(int $createur): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM itineraires  i
            JOIN itineraires_utilisateur iu ON i.id = iu.itineraires_id
            JOIN info_user u ON iu.utilisateur_id = u.user
            WHERE i.createur_id = :createur';
            
        $resultSet = $conn->executeQuery($sql, ['createur' => $createur]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function getUserByItineraire()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM itineraires i
            JOIN itineraires_utilisateur u ON i.id = u.itineraires_id
            JOIN utilisateur v ON u.utilisateur_id = v.id
            WHERE i.id = u.itineraires_id
            ';

        $resultSet = $conn->executeQuery($sql);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function getItineraireUsername()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT i.id, v.username FROM itineraires i
            JOIN itineraires_utilisateur u ON i.id = u.itineraires_id
            JOIN utilisateur v ON u.utilisateur_id = v.id
            WHERE i.id = u.itineraires_id
            ';

        $resultSet = $conn->executeQuery($sql);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAll();
    }

    public function filterByDistance(int $value = NULL)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * 
            FROM itineraires i
            JOIN itineraires_utilisateur u ON i.id = u.itineraires_id
            JOIN utilisateur v ON u.utilisateur_id = v.id
            WHERE i.distance < :value
            ORDER BY i.distance ASC 
            ';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('value', $value);

        $result = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $result->fetchAllAssociative();

    }

     /**
      * @return Question[] Returns an array of Question objects
      */
    public function findByDistance($value): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.distance < :val')
            ->setParameter('val', $value)
            ->orderBy('q.distance', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
      * @return Question[] Returns an array of Question objects
      */
    public function findByDuree($min,$max): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.duree > :min AND q.duree < :max')
            ->setParameter('min', $min)
            ->setParameter('max', $max)
            ->orderBy('q.duree', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
      * @return Question[] Returns an array of Question objects
      */
    public function findByNiveau($value): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.niveau = :val')
            ->setParameter('val', $value)
            ->orderBy('q.niveau', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
      * @return Question[] Returns an array of Question objects
      */
    public function findByKeyword($term): array
    {
        $termList = explode(' ', $term);
        return $this->createQueryBuilder('q')
            ->andWhere('q.titre LIKE :searchTerm OR q.description LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.$term.'%')            
            ->getQuery()
            ->getResult()
        ;
    }

    /**
      * @return Question[] Returns an array of Question objects
      */
    public function findByDepart($lieu): array
    {
        $lieuList = explode(' ', $lieu);
        return $this->createQueryBuilder('q')
            ->andWhere('q.depart LIKE :searchlieu')
            ->orderBy('q.depart', 'ASC')
            ->setParameter('searchlieu', '%'.$lieu.'%')            
            ->getQuery()
            ->getResult()
        ;
    }


   public function findByFields($lieu, $niveau, $distance, $min, $max, $term): array
   {
        $lieuList = explode(' ', $lieu);
        $termList = explode(' ', $term);
       return $this->createQueryBuilder('i')
           ->andWhere('i.depart LIKE :searchlieu OR i.niveau = :niveau OR i.distance < :distance OR i.duree BETWEEN :min AND :max OR i.titre LIKE :searchTerm OR i.description LIKE :searchTerm')
           ->setParameter('searchlieu', '%'.$lieu.'%')
           ->setParameter('niveau', $niveau)
           ->setParameter('distance', $distance)
           ->setParameter('min', $min)
           ->setParameter('max', $max)
           ->setParameter('searchTerm', '%'.$term.'%')  
           
           ->getQuery()
            ->getResult()
       ;
   }
}
