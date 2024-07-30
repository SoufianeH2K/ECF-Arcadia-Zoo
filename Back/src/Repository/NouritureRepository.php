<?php

namespace App\Repository;

use App\Entity\Nouriture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Nouriture>
 *
 * @method Nouriture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Nouriture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Nouriture[]    findAll()
 * @method Nouriture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NouritureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Nouriture::class);
    }

//    /**
//     * @return Nouriture[] Returns an array of Nouriture objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Nouriture
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
