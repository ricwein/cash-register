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
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Image;

class ProductCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        #[Autowire('%app.upload_dir_name%')] private readonly string $uploadDirName,
        #[Autowire('%app.upload_dir_path%')] private readonly string $uploadDirPath,
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
            AssociationField::new('events')->setTemplatePath('easy-admin/field/association_list.html.twig'),
            AssociationField::new('category'),
            AssociationField::new('additionalCategories')->setTemplatePath('easy-admin/field/association_list.html.twig'),
            ColorField::new('color'),
            IntegerField::new('priority'),
            ImageField::new('image')
                ->setBasePath($this->uploadDirName)
                ->setUploadDir($this->uploadDirPath)
                ->setFileConstraints(new Image(maxSize: '2M', maxWidth: 2048, maxHeight: 2048, detectCorrupted: true))
                ->setUploadedFileNamePattern('[slug]-[contenthash].[extension]')
            ,
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
