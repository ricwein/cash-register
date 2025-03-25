<?php

namespace App\Repository;

use App\Entity\PurchaseTransaction;
use App\Model\ReceiptArticle;
use App\Model\ReceiptFilter;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    /**
     * @return array<string, string>
     */
    public function findDistinctEvents(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $queryBuilder = $this->createQueryBuilder('transaction');
        $queryBuilder
            ->andWhere('transaction.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to);
        $queryBuilder
            ->select('transaction.eventName')
            ->addSelect('COUNT(transaction.id) AS quantity')
            ->groupBy('transaction.eventName');

        $events = [];
        foreach ($queryBuilder->getQuery()->getResult() as $event) {
            $events[$event['eventName']] = $event['quantity'];
        }
        return $events;
    }

    /**
     * @return array<string, array{int, float}>
     */
    public function findTransactionCountByDate(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        if ($from > $to) {
            return [];
        }
        $queryBuilder = $this->createQueryBuilder('transaction');
        $queryBuilder->leftJoin('transaction.soldArticles', 'article');
        $queryBuilder
            ->andWhere('transaction.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to);

        $queryBuilder
            ->select('count(transaction.id) as quantity')
            ->addSelect('DATE(transaction.createdAt) as created_date')
            ->addSelect('SUM(article.price * article.quantity) as price')
            ->groupBy('created_date');

        $resultSet = [];
        foreach ($queryBuilder->getQuery()->getResult() as $row) {
            $resultSet[$row['created_date']] = [(int)$row['quantity'], (float)$row['price']];
        }

        for (; $from <= $to; $from = $from->add(new DateInterval('P1D'))) {
            $resultSet[$from->format('Y-m-d')] ??= [0, 0.0];
        }

        ksort($resultSet);
        return $resultSet;
    }

    /**
     * @return array<string, ReceiptArticle[]>
     */
    public function aggregateArticlesByEvent(ReceiptFilter $filter): array
    {
        $queryBuilder = $this->createFilterQueryBuilder($filter);

        $queryBuilder
            ->select('transaction.eventName')
            ->addSelect('transaction.paymentType')
            ->addSelect('article.productName')
            ->addSelect('article.productId')
            ->addSelect('SUM(article.quantity) AS quantity')
            ->addSelect('SUM(article.price * article.quantity) AS price');

        $queryBuilder
            ->groupBy('transaction.eventName')
            ->addGroupBy('transaction.paymentType')
            ->addGroupBy('article.productName')
            ->addGroupBy('article.productId');

        $queryBuilder
            ->orderBy('transaction.eventName', 'ASC')
            ->addOrderBy('article.productName', 'ASC')
            ->addOrderBy('article.productId', 'ASC');

        $articles = [];
        foreach ($queryBuilder->getQuery()->getResult() as $articleData) {
            if (empty($articleData['productName'])) {
                continue;
            }

            $articles[$articleData['eventName']][] = new ReceiptArticle(
                name: $articleData['productName'],
                id: $articleData['productId'],
                quantity: $articleData['quantity'],
                price: $articleData['price'],
                paymentType: $articleData['paymentType'],
            );
        }

        return $articles;
    }

    /**
     * @return ReceiptArticle[]
     */
    public function aggregateArticlesOverall(ReceiptFilter $filter): array
    {
        $queryBuilder = $this->createFilterQueryBuilder($filter);

        $queryBuilder
            ->select('transaction.paymentType')
            ->addSelect('article.productName')
            ->addSelect('article.productId')
            ->addSelect('SUM(article.quantity) AS quantity')
            ->addSelect('SUM(article.price * article.quantity) AS price');

        $queryBuilder
            ->groupBy('article.productId')
            ->addGroupBy('transaction.paymentType');

        $queryBuilder
            ->orderBy('article.productName', 'ASC')
            ->addOrderBy('article.productId', 'ASC');

        $articles = [];
        foreach ($queryBuilder->getQuery()->getResult() as $articleData) {
            $articles[] = new ReceiptArticle(
                name: $articleData['productName'],
                id: $articleData['productId'],
                quantity: $articleData['quantity'],
                price: $articleData['price'],
                paymentType: $articleData['paymentType'],
            );
        }

        return $articles;
    }

    private function createFilterQueryBuilder(ReceiptFilter $filter): QueryBuilder
    {
        $fromDate = $filter->getFromDate()->setTime(0, 0);
        $toDate = ($filter->getToDate() ?? $fromDate)->setTime(23, 59);

        $queryBuilder = $this->createQueryBuilder('transaction');

        $queryBuilder
            ->andWhere('transaction.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $fromDate)
            ->setParameter('to', $toDate);

        if (!empty($filter->getEvents())) {
            $queryBuilder
                ->andWhere('transaction.eventName IN (:events)')
                ->setParameter('events', $filter->getEvents());
        }

        $queryBuilder
            ->leftJoin('transaction.soldArticles', 'article');

        return $queryBuilder;
    }
}
