<?php

namespace App\Repository;

use App\Entity\GasService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\QueryException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GasService|null find($id, $lockMode = null, $lockVersion = null)
 * @method GasService|null findOneBy(array $criteria, array $orderBy = null)
 * @method GasService[]    findAll()
 * @method GasService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository
 */
class GasServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GasService::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(GasService $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(GasService $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return mixed[]
     * @throws QueryException
     */
    public function findGasServiceByGasStationId()
    {
        $query = "SELECT s.label, t.id
            FROM gas_stations_services gs
            INNER JOIN gas_service s ON gs.gas_service_id = s.id
            INNER JOIN gas_station t ON gs.gas_station_id = t.id";

        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $results = $statement->executeQuery()->fetchAllAssociative();

        $data = [];
        foreach ($results as $result) {
            $data[$result['id']][$result['label']] = uniqid();
        }

        return $data;
    }

    public function findGasServiceById()
    {
        $query = $this->createQueryBuilder('t')
            ->select('t.id, t.reference, t.label')
            ->orderBy('t.reference', 'ASC')
            ->indexBy('t', 't.id')
            ->getQuery();

        return $query->getResult();
    }
}
