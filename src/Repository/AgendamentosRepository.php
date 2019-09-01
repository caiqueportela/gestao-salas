<?php

namespace App\Repository;

use App\Entity\Agendamentos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Agendamentos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Agendamentos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Agendamentos[]    findAll()
 * @method Agendamentos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgendamentosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Agendamentos::class);
    }

    // /**
    //  * @return Agendamentos[] Returns an array of Agendamentos objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Agendamentos
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
