<?php

namespace App\Repository;

use App\Entity\OperationTrace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OperationTrace|null find($id, $lockMode = null, $lockVersion = null)
 * @method OperationTrace|null findOneBy(array $criteria, array $orderBy = null)
 * @method OperationTrace[]    findAll()
 * @method OperationTrace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OperationTraceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OperationTrace::class);
    }

    // /**
    //  * @return OperationTrace[] Returns an array of OperationTrace objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OperationTrace
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
