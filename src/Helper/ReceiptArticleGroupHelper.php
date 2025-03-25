<?php

namespace App\Helper;

use App\Enum\PaymentType;
use App\Model\ReceiptArticle;

class ReceiptArticleGroupHelper
{
    /**
     * @param array<string, ReceiptArticle[]> $articles
     * @return array{
     *     array<string, array<string, ReceiptArticle>>,
     *     array<string, array<string, string>>
     * }
     */
    public function groupByEvents(array $articles): array
    {
        /** @var array<string, array<string, ReceiptArticle>> $groupedArticles */
        $groupedArticles = [];
        /** @var array<string, array<string, string>> $paymentPrices */
        $paymentPrices = [];

        foreach ($articles as $eventName => $eventArticles) {
            if (!isset($paymentPrices[$eventName])) {
                $paymentPrices[$eventName] = [
                    PaymentType::CARD->name => '0.00',
                    PaymentType::CASH->name => '0.00',
                ];
            }

            foreach ($eventArticles as $eventArticle) {
                $paymentType = $eventArticle->paymentType->name;
                if (array_key_exists($paymentType, $paymentPrices[$eventName])) {
                    $paymentPrices[$eventName][$paymentType] = bcadd(
                        $paymentPrices[$eventName][$paymentType],
                        $eventArticle->price,
                        2,
                    );
                }

                if (isset($groupedArticles[$eventName][$eventArticle->name])) {
                    $groupedArticles[$eventName][$eventArticle->name] = $groupedArticles[$eventName][$eventArticle->name]->add($eventArticle);
                } else {
                    $groupedArticles[$eventName][$eventArticle->name] = $eventArticle;
                }
            }
        }

        return [$groupedArticles, $paymentPrices];
    }

    /**
     * @param ReceiptArticle[] $articles
     * @return array{
     *     array<string, ReceiptArticle>,
     *     array<string, string>
     * }
     */
    public function group(array $articles): array
    {
        /** @var array<string, ReceiptArticle> $groupedArticles */
        $groupedArticles = [];
        /** @var array<string, string> $paymentPrices */
        $paymentPrices = [
            PaymentType::CARD->name => '0.00',
            PaymentType::CASH->name => '0.00',
        ];

        foreach ($articles as $article) {
            $paymentType = $article->paymentType->name;
            if (array_key_exists($paymentType, $paymentPrices)) {
                $paymentPrices[$paymentType] = bcadd(
                    $paymentPrices[$paymentType],
                    $article->price,
                    2,
                );
            }

            if (isset($groupedArticles[$article->name])) {
                $groupedArticles[$article->name] = $groupedArticles[$article->name]->add($article);
            } else {
                $groupedArticles[$article->name] = $article;
            }
        }

        return [$groupedArticles, $paymentPrices];
    }
}
