<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Registry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

class EventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('priority'),
            IntegerField::new('productsPerRow'),
            BooleanField::new('useCategoryTabs'),
            TextField::new('name'),
        ];
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

        /** @var Event $entity */
        $entity = $entityManager?->getRepository(self::getEntityFqcn())->find($id);

        $clone = clone $entity;
        foreach ($entity->getProducts() as $product) {
            $clone->addProduct(clone $product);
        }

        $entityManager?->persist($clone);
        $entityManager?->flush();

        return $this->redirectToRoute('admin_event_edit', ['entityId' => $clone->getId()]);
    }
}
