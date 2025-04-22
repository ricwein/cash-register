<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Enum\PaperSize;
use App\Form\ReceiptFilterType;
use App\Model\ReceiptFilter;
use App\Repository\PurchaseTransactionRepository;
use App\Service\ReceiptGenerator;
use DateInterval;
use DateInvalidOperationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ReceiptExportController extends AbstractController
{
    public const string DEFAULT_CHART_RANGE = 'P6M';

    public function __construct(
        private readonly ClockInterface $clock,
        private readonly PurchaseTransactionRepository $purchaseTransactionRepository,
        private readonly ReceiptGenerator $receiptGenerator,
        private readonly ChartBuilderInterface $chartBuilder,
        private readonly TranslatorInterface $translator,
    ) {}

    /**
     * @throws DateInvalidOperationException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    #[Route('/receipt-export', name: 'receipt-export')]
    public function index(Request $request): Response
    {
        $now = $this->clock->now();
        $fromDate = $now->sub(new DateInterval(self::DEFAULT_CHART_RANGE));

        $events = $this->purchaseTransactionRepository->findDistinctEvents(from: $fromDate, to: $now);
        foreach ($events as $name => $quantity) {
            $events[$name] = sprintf("%s (%s: %s)", $name, $this->translator->trans('PurchaseTransaction'), $quantity);
        }

        $form = $this->createForm(ReceiptFilterType::class, new ReceiptFilter($now), [
            'events' => array_flip($events),
        ]);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ReceiptFilter $filter */
            $filter = $form->getData();
            return $this->receiptGenerator->generateReceiptPDF(
                size: PaperSize::A4_LANDSCAPE,
                filter: $filter,
            );
        }

        $transactions = $this->purchaseTransactionRepository->findTransactionCountByDate(from: $fromDate, to: $now);
        $sales = $prices = [];
        foreach ($transactions as $date => $transaction) {
            $sales[$date] = $transaction[0];
            $prices[$date] = $transaction[1];
        }

        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'datasets' => [
                [
                    'label' => $this->translator->trans('Sold articles'),
                    'backgroundColor' => '#000000',
                    'borderColor' => '#000000',
                    'barPercentage' => 1.0,
                    'data' => $sales,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => $this->translator->trans('Price Sum'),
                    'backgroundColor' => '#FF0000',
                    'borderColor' => '#FF0000',
                    'barPercentage' => 1.0,
                    'data' => $prices,
                    'yAxisID' => 'y1',
                ],
            ]
        ]);
        $chart->setOptions([
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => ['display' => false],
                'tooltip' => ['padding' => 20, 'backgroundColor' => '#222', 'cornerRadius' => 1, 'boxPadding' => 0, 'usePointStyle' => true],
            ],
            'scales' => [
                'x' => [
                    'grid' => ['display' => false],
                    'ticks' => ['source' => 'labels'],
                ],
                'y' => [
                    'grid' => ['display' => false],
                    'title' => [
                        'display' => true,
                        'text' => $this->translator->trans('Sold articles'),
                        'color' => '#000000',
                    ],
                    'beginAtZero' => true,
                    'ticks' => ['stepSize' => 1],
                    'position' => 'left',
                ],
                'y1' => [
                    'grid' => ['display' => false],
                    'title' => [
                        'display' => true,
                        'text' => $this->translator->trans('Price Sum'),
                        'color' => '#FF0000',
                    ],
                    'beginAtZero' => true,
                    'position' => 'right',
                ],
            ]
        ]);

        return $this->render('admin/receipt_list.html.twig', [
            'chart' => $chart,
            'form' => $form,
        ]);
    }
}
