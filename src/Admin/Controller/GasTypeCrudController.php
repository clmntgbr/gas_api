<?php

namespace App\Admin\Controller;

use App\Entity\GasType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GasTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GasType::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::DELETE, Action::NEW, Action::EDIT);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('reference'),
            TextField::new('label'),
            DateTimeField::new('createdAt')
                ->setFormat('dd/MM/Y HH:mm:ss')
                ->renderAsNativeWidget(),
            DateTimeField::new('updatedAt')
                ->setFormat('dd/MM/Y HH:mm:ss')
                ->renderAsNativeWidget(),
        ];
    }
}
