<?php

namespace App\Repository\Messagerie;

use App\Entity\Messagerie\Discussions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Discussions>
 *
 * @method Discussions|null find($id, $lockMode = null, $lockVersion = null)
 * @method Discussions|null findOneBy(array $criteria, array $orderBy = null)
 * @method Discussions[]    findAll()
 * @method Discussions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiscussionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Discussions::class);
    }

   /**
    * @return Discussions[] Returns an array of Discussions objects
    */
   public function findDiscussionByUser($user1): array
   {
       $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * 
            FROM discussions d 
            JOIN utilisateur u ON d.user1_id = u.id;
            JOIN messages m ON m.discussion_id = d.id;
            WHERE discussions.user1_id = ?; 
            '
        ;
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $user1);

        $result = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $result->fetchAssociative();
   }

//    public function findOneBySomeField($value): ?Discussions
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
