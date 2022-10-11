<?php

namespace App\Admin\Controller;

use App\Entity\GasStation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class GasStationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GasStation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['updatedAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::DELETE);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('id')
            ->add('company')
            ->add('pop')
            ->add(TextFilter::new('address'))
            ->add(BooleanFilter::new('isFoundOnGouvMap'))
            ->add(DateTimeFilter::new('createdAt'))
            ->add(DateTimeFilter::new('updatedAt'))
            ->add(DateTimeFilter::new('closedAt'));
    }

    public function configureFields(string $pageName): iterable
    {
        if (Crud::PAGE_NEW === $pageName) {
            return [];
        }

        if (Crud::PAGE_DETAIL === $pageName) {
            return [
                IdField::new('id'),
                TextField::new('pop'),
                TextField::new('name'),
                TextField::new('company'),
                TextField::new('status'),
                ArrayField::new('gasServices'),
                AssociationField::new('address'),
                AssociationField::new('googlePlace'),
                ArrayField::new('gasServices'),
                DateTimeField::new('createdAt')
                    ->setFormat('dd/MM/Y HH:mm:ss')
                    ->renderAsNativeWidget(),
                DateTimeField::new('updatedAt')
                    ->setFormat('dd/MM/Y HH:mm:ss')
                    ->renderAsNativeWidget(),
                DateTimeField::new('closedAt')
                    ->setFormat('dd/MM/Y HH:mm:ss')
                    ->renderAsNativeWidget(),
            ];
        }

        if (Crud::PAGE_EDIT === $pageName) {
            return [
                IdField::new('id')
                    ->setFormTypeOption('disabled', 'disabled'),
                TextField::new('pop'),
                TextField::new('name'),
                TextField::new('company'),
                TextField::new('status'),
                ArrayField::new('gasServices'),
                AssociationField::new('address')
                    ->setFormTypeOption('disabled', 'disabled'),
                AssociationField::new('googlePlace')
                    ->setFormTypeOption('disabled', 'disabled'),
                DateTimeField::new('closedAt')
                    ->setFormat('dd/MM/Y HH:mm:ss')
                    ->renderAsNativeWidget()
                    ->setFormTypeOption('disabled', 'disabled'),
                DateTimeField::new('createdAt')
                    ->setFormat('dd/MM/Y HH:mm:ss')
                    ->renderAsNativeWidget()
                    ->setFormTypeOption('disabled', 'disabled'),
                DateTimeField::new('updatedAt')
                    ->setFormat('dd/MM/Y HH:mm:ss')
                    ->renderAsNativeWidget()
                    ->setFormTypeOption('disabled', 'disabled'),
            ];
        }

        if (Crud::PAGE_INDEX === $pageName) {
            return [
                IdField::new('id')->setMaxLength(15),
                TextField::new('name'),
                TextField::new('pop'),
                TextField::new('status'),
                AssociationField::new('address'),
                AssociationField::new('googlePlace'),
                DateTimeField::new('closedAt')
                    ->setFormat('dd/MM/Y HH:mm:ss')
                    ->renderAsNativeWidget(),
                DateTimeField::new('createdAt')
                    ->setFormat('dd/MM/Y HH:mm:ss')
                    ->renderAsNativeWidget(),
                DateTimeField::new('updatedAt')
                    ->setFormat('dd/MM/Y HH:mm:ss')
                    ->renderAsNativeWidget(),
            ];
        }

        return [];
    }
}