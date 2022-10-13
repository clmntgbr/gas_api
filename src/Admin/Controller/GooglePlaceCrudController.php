<?php

namespace App\Admin\Controller;

use App\Entity\GooglePlace;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class GooglePlaceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GooglePlace::class;
    }
}
