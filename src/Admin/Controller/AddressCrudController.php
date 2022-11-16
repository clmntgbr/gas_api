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

class AddressCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Address::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['updatedAt' => 'DESC']);
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
            ->add('id')
            ->add('city')
            ->add('region')
            ->add('postalCode')
            ->add('country');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('vicinity'),
            TextField::new('street'),
            TextField::new('number'),
            TextField::new('city'),
            TextField::new('region'),
            TextField::new('postalCode'),
            TextField::new('country'),
            TextField::new('longitude')->hideOnIndex(),
            TextField::new('latitude')->hideOnIndex(),
            DateTimeField::new('createdAt')
                ->setFormat('dd/MM/Y HH:mm:ss')
                ->renderAsNativeWidget()->hideOnIndex()->setDisabled(),
            DateTimeField::new('updatedAt')
                ->setFormat('dd/MM/Y HH:mm:ss')
                ->renderAsNativeWidget()->hideOnIndex()->setDisabled(),
        ];
    }
}