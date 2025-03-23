<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\Product;
use App\Entity\PurchaseTransaction;
use App\Entity\Setting;
use App\Repository\EventRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
    ) {}

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('CashRegister')
            ->setFaviconPath('favicon.svg')
            ->disableDarkMode()
            ->generateRelativeUrls();
    }

    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'events' => $this->eventRepository->findAll(),
        ]);
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addWebpackEncoreEntry('admin');
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
