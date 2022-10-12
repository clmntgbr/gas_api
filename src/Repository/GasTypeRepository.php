<?php

namespace App\Repository;

use App\Entity\GasType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\QueryException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GasType>
 *
 * @method GasType|null find($id, $lockMode = null, $lockVersion = null)
 * @method GasType|null findOneBy(array $criteria, array $orderBy = null)
 * @method GasType[]    findAll()
 * @method GasType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GasTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GasType::class);
    }

    public function save(GasType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GasType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return mixed[]
     * @throws QueryException
     */
    public function findGasTypeById(): array
    {
        $query = $this->createQueryBuilder('t')
            ->select('t.id, t.reference, t.label')
            ->orderBy('t.id', 'ASC')
            ->indexBy('t', 't.id')
            ->getQuery();

        return $query->getResult();
    }

//    /**
//     * @return GasType[] Returns an array of GasType objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GasType
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
