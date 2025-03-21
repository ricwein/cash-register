<?php

namespace App\Controller\Admin;

use App\Entity\PurchaseTransaction;
use App\Enum\PaymentType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;

class PurchaseTransactionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PurchaseTransaction::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('transactionId')->setMaxLength(36)->addCssClass('font-monospace small text-dark'),
            TextField::new('eventName'),
            ChoiceField::new('paymentType'),
            MoneyField::new('price', 'Price Sum')->setCurrency('EUR')->setStoredAsCents(false),
            DateTimeField::new('createdAt'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add(
            ChoiceFilter::new('paymentType')->setChoices([
                'Karte' => PaymentType::CARD->value,
                'Bar' => PaymentType::CASH->value,
                '-keine-' => PaymentType::NONE->value,
            ])
        );
    }
}
