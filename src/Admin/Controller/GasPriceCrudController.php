<?php

namespace App\Admin\Controller;

use App\Entity\GasPrice;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class GasPriceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GasPrice::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['date' => 'DESC', 'gasStation' => 'ASC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('gasType')
            ->add(TextFilter::new('gasStation'))
            ->add('currency')
            ->add(DateTimeFilter::new('date'))
            ->add(DateTimeFilter::new('createdAt'))
            ->add(DateTimeFilter::new('updatedAt'));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::DELETE, Action::EDIT);
    }

    public function configureFields(string $pageName): iterable
    {
        if (Crud::PAGE_NEW === $pageName) {
            return [];
        }

        if (Crud::PAGE_EDIT === $pageName) {
            return [];
        }

        if (Crud::PAGE_DETAIL === $pageName) {
            return [
                IdField::new('id'),
                AssociationField::new('gasType'),
                AssociationField::new('gasStation'),
                IntegerField::new('value'),
                DateTimeField::new('date')->setFormat('dd/MM/Y HH:mm:ss')->renderAsNativeWidget(),
                DateTimeField::new('createdAt')->setFormat('dd/MM/Y HH:mm:ss')->renderAsNativeWidget(),
                DateTimeField::new('updatedAt')->setFormat('dd/MM/Y HH:mm:ss')->renderAsNativeWidget(),
                AssociationField::new('currency'),
            ];
        }

        if (Crud::PAGE_INDEX === $pageName) {
            return [
                IdField::new('id'),
                AssociationField::new('gasType'),
                AssociationField::new('gasStation'),
                IntegerField::new('value'),
                DateTimeField::new('date')->setFormat('dd/MM/Y HH:mm:ss')->renderAsNativeWidget(),
                DateTimeField::new('createdAt')->setFormat('dd/MM/Y HH:mm:ss')->renderAsNativeWidget(),
                DateTimeField::new('updatedAt')->setFormat('dd/MM/Y HH:mm:ss')->renderAsNativeWidget(),
                AssociationField::new('currency'),
            ];
        }

        return [];
    }
}
