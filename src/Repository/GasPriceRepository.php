<?php

namespace App\Repository;

use App\Entity\GasPrice;
use App\Entity\GasStation;
use App\Entity\GasType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GasPrice>
 *
 * @method GasPrice|null find($id, $lockMode = null, $lockVersion = null)
 * @method GasPrice|null findOneBy(array $criteria, array $orderBy = null)
 * @method GasPrice[]    findAll()
 * @method GasPrice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GasPriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GasPrice::class);
    }

    public function save(GasPrice $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GasPrice $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findGasPricesByYear(GasStation $gasStation, GasType $gasType, string $year): array
    {
        return $this->createQueryBuilder('g')
            ->select('g, WEEK(g.date) AS week')
            ->innerJoin('g.gasType', 't')
            ->innerJoin('g.gasStation', 's')
            ->where('t.id = :gasTypeId AND s.id = :gasStationId')
            ->andWhere(sprintf("g.date >= '%s' AND g.date <= '%s'", "$year-01-01 00:00:00", "$year-12-31 23:59:59"))
            ->setParameter('gasTypeId', $gasType->getId())
            ->setParameter('gasStationId', $gasStation->getId())
            ->orderBy('g.date', 'ASC')
            ->groupBy('week')
            ->getQuery()
            ->getResult();
    }
}