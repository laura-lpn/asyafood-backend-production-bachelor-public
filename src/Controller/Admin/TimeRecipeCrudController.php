<?php

namespace App\Controller\Admin;

use App\Entity\TimeRecipe;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;


class TimeRecipeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TimeRecipe::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('value'),
            AssociationField::new('timeType'),
            AssociationField::new('recipe')
        ];
    }
}
