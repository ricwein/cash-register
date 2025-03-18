<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Registry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('priority'),
            TextField::new('name'),
            ColorField::new('color'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(
                Crud::PAGE_INDEX,
                Action::new('clone')->linkToCrudAction('cloneAction')
            );
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function cloneAction(AdminContext $context): Response
    {
        $id = $context->getRequest()->query->get('entityId');

        /** @var Registry $doctrine */
        $doctrine = $this->container->get('doctrine');
        $entityManager = $doctrine->getManagerForClass(self::getEntityFqcn());

        /** @var Category $entity */
        $entity = $entityManager?->getRepository(self::getEntityFqcn())->find($id);

        $clone = clone $entity;
        $entityManager?->persist($clone);
        $entityManager?->flush();

        return $this->redirectToRoute('admin_category_edit', ['entityId' => $clone->getId()]);
    }
}
