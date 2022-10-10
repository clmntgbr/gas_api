<?php

namespace App\Admin\Controller;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addPanel('User Details'),
            IdField::new('id')->setDisabled(true),
            TextField::new('email'),
            TextField::new('name'),

            FormField::addPanel('Image'),
            TextField::new('imageFile', 'Upload')
                ->setFormType(VichImageType::class)
                ->onlyOnForms(),
            ImageField::new('image.name', 'Image')
                ->setRequired(true)
                ->setBasePath('/images/users/')
                ->hideOnForm(),
            TextField::new('image.name', 'Name')->setDisabled()->hideOnIndex(),
            TextField::new('image.originalName', 'originalName')->setDisabled()->hideOnIndex(),
            NumberField::new('image.size', 'Size')->setDisabled()->hideOnIndex(),
            TextField::new('image.mimeType', 'mimeType')->setDisabled()->hideOnIndex(),
            ArrayField::new('image.dimensions', 'Dimensions')->setDisabled()->hideOnIndex(),
        ];
    }
}
