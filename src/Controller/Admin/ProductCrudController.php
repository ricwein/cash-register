<?php

namespace App\Controller\Admin;

use App\EasyAdmin\Field\IconField;
use App\Entity\Product;
use App\Repository\EventRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

class ProductCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
    ) {}

    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IconField::new('icon'),
            TextField::new('name'),
            AssociationField::new('events'),
            AssociationField::new('category'),
            ColorField::new('color'),
            IntegerField::new('priority'),
            ImageField::new('image')->setBasePath('uploads')->setUploadDir('public/uploads'),
            MoneyField::new('price')->addCssClass('fw-bold')->setCurrency('EUR')->setStoredAsCents(false),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        $events = $this->eventRepository->findAllEventNames();
        if (empty($events)) {
            return $filters;
        }

        return $filters->add(
            ChoiceFilter::new('events')
                ->setChoices($this->eventRepository->findAllEventNames())
                ->canSelectMultiple()
        );
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            /** {@see self::cloneAction()} */
            ->add(Crud::PAGE_INDEX, Action::new('clone')->linkToCrudAction('cloneAction'));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function cloneAction(AdminContext $context): Response
    {
        $id = $context->getRequest()->query->get('entityId');

        /** @var Registry $doctrine */
        $doctrine = $this->container->get('doctrine');
        $entityManager = $doctrine->getManagerForClass(self::getEntityFqcn());

        /** @var Product $entity */
        $entity = $entityManager?->getRepository(self::getEntityFqcn())->find($id);

        $clone = clone $entity;
        $entityManager?->persist($clone);
        $entityManager?->flush();

        return $this->redirectToRoute('admin_product_edit', ['entityId' => $clone->getId()]);
    }
}
