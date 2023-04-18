<?php

namespace App\Repository;

use App\Entity\LocationSportCenter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LocationSportCenter>
 *
 * @method LocationSportCenter|null find($id, $lockMode = null, $lockVersion = null)
 * @method LocationSportCenter|null findOneBy(array $criteria, array $orderBy = null)
 * @method LocationSportCenter[]    findAll()
 * @method LocationSportCenter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationSportCenterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocationSportCenter::class);
    }

    public function add(LocationSportCenter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LocationSportCenter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return LocationSportCenter[] Returns an array of LocationSportCenter objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LocationSportCenter
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
