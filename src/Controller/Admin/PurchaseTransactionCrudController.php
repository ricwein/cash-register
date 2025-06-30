<?php

namespace App\Controller\Admin;

use App\EasyAdmin\Filter\DateFilter;
use App\Entity\PurchaseTransaction;
use App\Enum\PaymentType;
use App\Repository\PurchaseTransactionRepository;
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
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class PurchaseTransactionCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly PurchaseTransactionRepository $repository,
    ) {}

    public static function getEntityFqcn(): string
    {
        return PurchaseTransaction::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('transactionId')->setMaxLength(36)->addCssClass('font-monospace small text-dark'),
            TextField::new('eventName'),
            ChoiceField::new('paymentType')->setTemplatePath('easy-admin/field/badge.html.twig'),
            MoneyField::new('price', 'Price Sum')->setCurrency('EUR')->setStoredAsCents(false),
            DateTimeField::new('createdAt'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT);
    }

    public function configureFilters(Filters $filters): Filters
    {
        $events = $this->repository->findDistinctEventNames();
        return $filters
            ->add(
                ChoiceFilter::new('paymentType')->setChoices([
                    'Karte' => PaymentType::CARD->value,
                    'Bar' => PaymentType::CASH->value,
                    '-keine-' => PaymentType::NONE->value,
                ])
            )
            ->add(
                ChoiceFilter::new('eventName')->setChoices($events)
            )
            ->add(
                DateFilter::new('createdAt')
            )
            ->add(
                NumericFilter::new('price')->setFormTypeOption('value_type', MoneyType::class)
            );
    }
}
