<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @return Category[]
     */
    public function findAllForEvent(Event $event): array
    {
        $queryBuilder = $this->createQueryBuilder('category');
        $queryBuilder
            ->leftJoin('category.products', 'product')
            ->addSelect('product');

        $queryBuilder
            ->where('product.event = :eventId')
            ->setParameter('eventId', $event->getId());

        $queryBuilder
            ->orderBy('category.priority', 'ASC')
            ->addOrderBy('category.id', 'ASC')
            ->addOrderBy('product.priority', 'ASC')
            ->addOrderBy('product.id', 'ASC');

        return $queryBuilder->getQuery()->getResult();

    }

}
