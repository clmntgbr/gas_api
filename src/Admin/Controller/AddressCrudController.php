<?php

namespace App\Admin\Controller;

use App\Entity\Address;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;

class AddressCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Address::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::DELETE, Action::NEW);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('city')
            ->add('region')
            ->add('postalCode')
            ->add('country')
            ->add(DateTimeFilter::new('createdAt'))
            ->add(DateTimeFilter::new('updatedAt'));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('vicinity'),
            TextField::new('street'),
            TextField::new('number'),
            TextField::new('city'),
            TextField::new('region'),
            TextField::new('postalCode'),
            TextField::new('country'),
            TextField::new('longitude'),
            TextField::new('latitude'),
            DateTimeField::new('createdAt')
                ->setFormat('dd/MM/Y HH:mm:ss')
                ->renderAsNativeWidget(),
            DateTimeField::new('updatedAt')
                ->setFormat('dd/MM/Y HH:mm:ss')
                ->renderAsNativeWidget(),
        ];
    }
}
