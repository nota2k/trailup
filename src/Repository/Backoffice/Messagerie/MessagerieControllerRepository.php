<?php

namespace App\Repository\Backoffice\Messagerie;

use App\Entity\Backoffice\Messagerie\MessagerieController;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MessagerieController>
 *
 * @method MessagerieController|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessagerieController|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessagerieController[]    findAll()
 * @method MessagerieController[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessagerieControllerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessagerieController::class);
    }

//    /**
//     * @return MessagerieController[] Returns an array of MessagerieController objects
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

//    public function findOneBySomeField($value): ?MessagerieController
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
