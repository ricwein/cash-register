<?php

namespace App\Controller\Admin;

use App\Entity\SalesTax;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\PercentField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SalesTaxCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SalesTax::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            PercentField::new('percent')->setNumDecimals(2)->setStoredAsFractional(false),
        ];
    }
}
