<?php

namespace App\Form\Type\Admin;

use App\Lists\GasStationStatusReference;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GasStationStatusFilterType extends AbstractType
{
    public function __construct()
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => GasStationStatusReference::getConstantsList(),
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
