<?php

namespace App\Repository;

use App\Entity\EvenPlayers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EvenPlayers>
 *
 * @method EvenPlayers|null find($id, $lockMode = null, $lockVersion = null)
 * @method EvenPlayers|null findOneBy(array $criteria, array $orderBy = null)
 * @method EvenPlayers[]    findAll()
 * @method EvenPlayers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvenPlayersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EvenPlayers::class);
    }

    public function add(EvenPlayers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EvenPlayers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return EvenPlayers[] Returns an array of EvenPlayers objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EvenPlayers
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
