<?php

namespace App\Repository\Messagerie;

use App\Entity\Messagerie\Messages;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Messages>
 *
 * @method Messages|null find($id, $lockMode = null, $lockVersion = null)
 * @method Messages|null findOneBy(array $criteria, array $orderBy = null)
 * @method Messages[]    findAll()
 * @method Messages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Messages::class);
    }

//    /**
//     * @return Messages[] Returns an array of Messages objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findById($user1)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * 
            FROM messages m 
            JOIN discussions d ON m.discussion_id = d.id;
            WHERE discussions.user1_id = ?; 
            '
        ;
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $user1);

        $result = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $result->fetchAssociative();
    }

    /**
     * Compte les messages non lus pour un utilisateur donné
     * Un message est non lu s'il n'a pas été envoyé par l'utilisateur et que lu = false
     */
    public function countUnreadMessages(Utilisateur $user): int
    {
        try {
            // Utiliser une requête DQL plus simple et robuste
            $qb = $this->createQueryBuilder('m')
                ->join('m.discussion', 'd')
                ->where('(d.user1 = :user OR d.user2 = :user)')
                ->andWhere('m.expediteur != :user')
                ->andWhere('m.lu = :falseValue')
                ->setParameter('user', $user)
                ->setParameter('falseValue', false);
            
            $result = $qb->select('COUNT(m.id)')
                ->getQuery()
                ->getSingleScalarResult();
            
            return (int) $result;
        } catch (\Exception $e) {
            // En cas d'erreur, retourner 0 et logger l'erreur
            error_log('Erreur lors du comptage des messages non lus: ' . $e->getMessage());
            return 0;
        }
    }

}
