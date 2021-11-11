<?php

namespace App\Repository;

use App\Entity\Wilaya;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Wilaya|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wilaya|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wilaya[]    findAll()
 * @method Wilaya[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WilayaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wilaya::class);
    }

    // /**
    //  * @return Wilaya[] Returns an array of Wilaya objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Wilaya
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
