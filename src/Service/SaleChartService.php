<?php

namespace App\Service;

use App\Entity\Sale;
use DateInterval;
use DateTimeImmutable;

readonly class SaleChartService
{
    public function __construct(
        private string $format = 'd.m.Y',
    ) {}

    /**
     * @param Sale[] $sales
     */
    public function buildData(array $sales, DateTimeImmutable $startDate, DateTimeImmutable $endDate): array
    {
        $data = [];
        while ($startDate <= $endDate) {
            $data[$startDate->format($this->format)] = 0.00;
            $startDate = $startDate->add(new DateInterval('P1D'));
        }

        foreach ($sales as $sale) {
            $day = $sale->getCreatedAt()->format('d.m.Y');
            if (isset($data[$day])) {
                $data[$day] = (float)$sale->getPrice() + $data[$day];
            } else {
                $data[$day] = (float)$sale->getPrice();
            }
        }

        return [
            'datasets' => [[
                'label' => 'Sales',
                'data' => $data,
            ]]
        ];
    }
}
