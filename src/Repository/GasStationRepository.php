<?php

namespace App\Repository;

use App\Entity\GasStation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\QueryException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GasStation>
 *
 * @method GasStation|null find($id, $lockMode = null, $lockVersion = null)
 * @method GasStation|null findOneBy(array $criteria, array $orderBy = null)
 * @method GasStation[]    findAll()
 * @method GasStation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GasStationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GasStation::class);
    }

    public function save(GasStation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GasStation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return GasStation[]|null
     */
    public function findGasStationNotClosed()
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.closedAt is null')
            ->orderBy('s.updatedAt', 'DESC')
            ->setMaxResults(15)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return GasStation[]
     *
     * @throws QueryException
     */
    public function getGasStationGooglePlaceByPlaceId(GasStation $gasStation)
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->innerJoin('s.googlePlace', 'ss')
            ->where('ss.placeId = :placeId AND ss.placeId IS NOT NULL')
            ->setParameter('placeId', $gasStation->getGooglePlace()->getPlaceId())
            ->getQuery();

        return $query->getResult();
    }

    /** @return GasStation[] */
    public function getGasStationsMap(string $longitude, string $latitude, string $radius)
    {
        $query = "  SELECT s.id, (SQRT(POW(69.1 * (a.latitude - $latitude), 2) + POW(69.1 * ($longitude - a.longitude) * COS(a.latitude / 57.3), 2))*1000) as distance
                    FROM gas_station s 
                    INNER JOIN address a ON s.address_id = a.id
                    WHERE a.longitude IS NOT NULL AND a.latitude IS NOT NULL AND s.status != 'closed'
                    HAVING `distance` < $radius
                    ORDER BY `distance` ASC LIMIT 100;
        ";

        $statement = $this->getEntityManager()->getConnection()->prepare($query);

        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.id IN (:ids)')
            ->setParameter('ids', $statement->executeQuery()->fetchFirstColumn())
            ->getQuery();

        return $query->getResult();
    }
}
