<?php

namespace App\Repository;

use App\Entity\SalesPerson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SalesPerson>
 *
 * @method SalesPerson|null find($id, $lockMode = null, $lockVersion = null)
 * @method SalesPerson|null findOneBy(array $criteria, array $orderBy = null)
 * @method SalesPerson[]    findAll()
 * @method SalesPerson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SalesPersonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SalesPerson::class);
    }

//    /**
//     * @return SalesPerson[] Returns an array of SalesPerson objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SalesPerson
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
