<?php

namespace App\Repository;

use App\Entity\Salas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Salas|null find($id, $lockMode = null, $lockVersion = null)
 * @method Salas|null findOneBy(array $criteria, array $orderBy = null)
 * @method Salas[]    findAll()
 * @method Salas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SalasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Salas::class);
    }

    // /**
    //  * @return Salas[] Returns an array of Salas objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Salas
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
