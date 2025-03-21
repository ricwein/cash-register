<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\PurchaseTransactionRepository;
use DateInterval;
use DateInvalidOperationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class ReceiptExportController extends AbstractController
{
    public function __construct(
        private readonly ClockInterface $clock,
        private readonly PurchaseTransactionRepository $purchaseTransactionRepository,
    ) {}

    /**
     * @throws DateInvalidOperationException
     */
    #[Route('/receipt-export', name: 'receipt-export')]
    public function index(ChartBuilderInterface $chartBuilder, TranslatorInterface $translator): Response
    {
        $now = $this->clock->now();
        $transactionHistory = $this->purchaseTransactionRepository->findTransactionCountByDate(
            from: $now->sub(new DateInterval('P1Y')),
            to: $now,
        );

        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'datasets' => [[
                'label' => $translator->trans('Transactions'),
                'backgroundColor' => 'black',
                'borderColor' => 'black',
                'barPercentage' => 1.0,
                'data' => $transactionHistory,
            ]]
        ]);
        $chart->setOptions([
            'maintainAspectRatio' => false,
            'plugins' => ['legend' => ['display' => false]],
            'scales' => [
                'x' => ['grid' => ['display' => false], 'ticks' => ['source' => 'labels']],
                'y' => ['grid' => ['display' => false], 'beginAtZero' => true, 'ticks' => ['stepSize' => 1], 'position' => 'right'],
            ]
        ]);
        return $this->render('admin/receipt_list.html.twig', [
            'chart' => $chart,
        ]);
    }
}
