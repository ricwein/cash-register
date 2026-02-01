<?php

namespace App\Helper;

use App\Enum\PaymentType;
use App\Model\ReceiptArticle;
use BcMath\Number;

class ReceiptArticleGroupHelper
{
    public const int PRECISION = 2;

    /**
     * @param array<string, array<int, ReceiptArticle[]>> $articles
     * @return array{
     *     array<string, array<int, array<string, ReceiptArticle>>>,
     *     array<string, array<int, array<string, Number>>>
     * }
     */
    public function groupByEvents(array $articles): array
    {
        /** @var array<string, array<int, array<string, ReceiptArticle>>> $groupedArticles */
        $groupedArticles = [];
        /** @var array<string, array<int, array<string, Number>>> $paymentPrices */
        $paymentPrices = [];

        foreach ($articles as $eventName => $eventArticles) {
            foreach ($eventArticles as $tax => $taxArticles) {
                [$articleGroup, $pricesPerPaymentType] = $this->groupArticles($taxArticles);

                $paymentPrices[$eventName][$tax] = $pricesPerPaymentType;
                $groupedArticles[$eventName][$tax] = $articleGroup;
            }
        }

        return [$groupedArticles, $paymentPrices];
    }

    /**
     * @param array<int, ReceiptArticle[]> $articles
     * @return array{
     *     array<int, array<string, ReceiptArticle>>,
     *     array<int, array<string, Number>>
     * }
     */
    public function group(array $articles): array
    {
        /** @var array<int, array<string, ReceiptArticle>> $groupedArticles */
        $groupedArticles = [];
        /** @var array<int, array<string, Number>> $paymentPrices */
        $paymentPrices = [];

        foreach ($articles as $tax => $taxArticles) {
            [$articleGroup, $pricesPerPaymentType] = $this->groupArticles($taxArticles);

            $paymentPrices[$tax] = $pricesPerPaymentType;
            $groupedArticles[$tax] = $articleGroup;
        }

        return [$groupedArticles, $paymentPrices];
    }

    /**
     * @param ReceiptArticle[] $articles
     * @return array{
     *     array<string, ReceiptArticle>,
     *     array<string, Number>
     * }
     */
    private function groupArticles(array $articles): array
    {
        $pricesPerPaymentType = [
            PaymentType::CARD->name => new Number('0.00'),
            PaymentType::CASH->name => new Number('0.00'),
            PaymentType::NONE->name => new Number('0.00'),
        ];
        /** @var array<string, ReceiptArticle> $articleGroup */
        $articleGroup = [];

        foreach ($articles as $article) {
            $paymentType = $article->paymentType?->name ?? PaymentType::NONE->name;
            if (array_key_exists($paymentType, $pricesPerPaymentType)) {
                $pricesPerPaymentType[$paymentType] = $pricesPerPaymentType[$paymentType]
                    ->add($article->price, self::PRECISION);
            }

            if (isset($articleGroup[$article->name])) {
                $articleGroup[$article->name] = $articleGroup[$article->name]->add($article);
            } else {
                $articleGroup[$article->name] = $article;
            }
        }

        return [$articleGroup, $pricesPerPaymentType];
    }
}
