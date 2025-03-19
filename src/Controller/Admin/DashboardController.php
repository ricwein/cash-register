<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\Product;
use App\Entity\Setting;
use App\Entity\PurchaseTransaction;
use App\Repository\EventRepository;
use App\Service\SaleChartService;
use DateInterval;
use DateInvalidOperationException;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[AdminDashboard(routePath: '', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly ClockInterface $clock,
        private readonly EventRepository $eventRepository,
        private readonly SaleChartService $saleChartService,
        private readonly ChartBuilderInterface $chartBuilder,
    ) {}

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('CashRegister')
            ->setFaviconPath('favicon.svg')
            ->disableDarkMode();
    }

    /**
     * @throws DateInvalidOperationException
     */
    public function index(): Response
    {
        $now = $this->clock->now();
        $fromDate = $now->sub(new DateInterval('P7D'));
        $dataset = $this->saleChartService->buildData(
            sales: [],
            startDate: $fromDate,
            endDate: $now,
        );

        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData($dataset);

        return $this->render('admin/dashboard.html.twig', [
            'events' => $this->eventRepository->findAll(),
            'chart' => $chart,
        ]);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Management');
        yield MenuItem::linkToCrud('Events', 'fa-solid fa-list', Event::class);
        yield MenuItem::linkToCrud('Categories', 'fa-solid fa-list', Category::class);
        yield MenuItem::linkToCrud('Products', 'fa-solid fa-list', Product::class);
        yield MenuItem::section('Register');
        yield MenuItem::linkToRoute('Export Receipts', 'fa-solid fa-receipt', 'receipt-export');
        yield MenuItem::linkToCrud('Transactions', 'fa-solid fa-receipt', PurchaseTransaction::class);
        yield MenuItem::section('Setting');
        yield MenuItem::linkToCrud('Setting', 'fa-solid fa-gears', Setting::class);
    }
}
