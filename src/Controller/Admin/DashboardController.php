<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\Product;
use App\Entity\Sale;
use App\Repository\EventRepository;
use App\Repository\SaleRepository;
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
        private readonly SaleRepository $saleRepository,
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
        $sales = $this->saleRepository->findSales(since: $fromDate);
        $dataset = $this->saleChartService->buildData(
            sales: $sales,
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
        yield MenuItem::section('Verwaltung');
        yield MenuItem::linkToCrud('Events', 'fas fa-list', Event::class);
        yield MenuItem::linkToCrud('Categories', 'fas fa-list', Category::class);
        yield MenuItem::linkToCrud('Products', 'fas fa-list', Product::class);
        yield MenuItem::section('Kasse');
        yield MenuItem::linkToCrud('Sales', 'fas fa-list', Sale::class);
    }
}
