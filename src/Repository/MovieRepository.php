<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Movie>
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    public function findAllFiltered(array $filter = [])
    {
        $results = null;

        if (!empty($filter) && !is_null($filter['field']) && !is_null($filter['value'])) {
            $results = $this->createQueryBuilder('m')
                ->andWhere('LOWER('.$filter['field'].') LIKE :value')
                ->setParameter('value', '%'.strtolower($filter['value']).'%')
                ->getQuery()
                ->execute()
            ;
        }
        else {
            $results = $this->findAll();
        }

        return $results;
    }
}
