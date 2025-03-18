<?php

namespace App\Repository;

use App\Entity\Sale;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sale>
 */
class SaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sale::class);
    }

    /**
     * @return Sale[]
     */
    public function findSales(DateTimeImmutable $since): array
    {
        $queryBuilder = $this
            ->createQueryBuilder('sale');
        $queryBuilder
            ->andWhere('sale.createdAt >= :since')
            ->setParameter('since', $since);
        return $queryBuilder->getQuery()->getResult();
    }
}
