<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReceiptExportController extends AbstractController
{
    #[Route('/receipt-export', name: 'receipt-export')]
    public function index(): Response
    {
        return $this->render('admin/receipt_list.html.twig');
    }
}
