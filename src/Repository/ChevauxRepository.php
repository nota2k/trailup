<?php

namespace App\Repository;

use App\Entity\Chevaux;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chevaux>
 *
 * @method Chevaux|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chevaux|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chevaux[]    findAll()
 * @method Chevaux[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChevauxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chevaux::class);
    }

    public function ownerHorseById($id) {
        $qb = $this->createQueryBuilder('p')
                ->select('p, d') // Entity Utilisateur
                ->join('p.proprietaire', 'd')
                ->where('p.id = :id')
                ->setParameter('id', $id);

        return $qb->getQuery()->getResult();      
    }

//    /**
//     * @return Chevaux[] Returns an array of Chevaux objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Chevaux
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
