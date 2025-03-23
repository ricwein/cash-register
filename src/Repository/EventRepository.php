<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @param string[] $eventNames
     * @return Event[]
     */
    public function findProductsFor(array $eventNames): array
    {
        $queryBuilder = $this->createQueryBuilder('event');
        $queryBuilder
            ->andWhere('event.name IN (:eventNames)')
            ->setParameter('eventNames', $eventNames);

        $queryBuilder
            ->leftJoin('event.products', 'product')
            ->addSelect('product');

        return $queryBuilder->getQuery()->getResult();
    }

    public function findAllEventNames(): array
    {
        $queryBuilder = $this->createQueryBuilder('event');
        $queryBuilder->select('event.name');
        $query = $queryBuilder->getQuery();

        $result = [];
        foreach ($query->getResult() as $event) {
            $result[$event['name']] = $event['name'];
        }
        return $result;
    }
}
