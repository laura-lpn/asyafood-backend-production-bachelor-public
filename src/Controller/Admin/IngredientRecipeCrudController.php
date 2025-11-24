<?php

namespace App\Controller\Admin;

use App\Entity\IngredientRecipe;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class IngredientRecipeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return IngredientRecipe::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            NumberField::new('quantity'),
            AssociationField::new('unit'),
            AssociationField::new('ingredient'),
            AssociationField::new('recipe')
        ];
    }
}