<?php

namespace App\Repository;

use App\Entity\PurchaseTransaction;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PurchaseTransaction>
 */
class PurchaseTransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseTransaction::class);
    }

    public function findTransactionCountByDate(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        if ($from > $to) {
            return [];
        }
        $queryBuilder = $this->createQueryBuilder('transaction');
        $queryBuilder
            ->andWhere('transaction.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to);

        $queryBuilder
            ->select('count(transaction.id) as quantity')
            ->addSelect('DATE(transaction.createdAt) as created_date')
            ->groupBy('created_date');

        $resultSet = [];
        foreach ($queryBuilder->getQuery()->getResult() as $row) {
            $resultSet[$row['created_date']] = (int)$row['quantity'];
        }

        for (; $from <= $to; $from = $from->add(new DateInterval('P1D'))) {
            $resultSet[$from->format('Y-m-d')] ??= 0;
        }

        ksort($resultSet);
        return $resultSet;
    }
}
