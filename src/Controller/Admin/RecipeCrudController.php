<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class RecipeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Recipe::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            TextareaField::new('metaDescription'),
            
            SlugField::new('slug', 'Slug')->setTargetFieldName('name'),

            ImageField::new('printName')->setBasePath('/recipes/pdf/recipes_prints')->onlyOnDetail(),
            TextareaField::new('printFile')->setFormType(VichFileType::class)->onlyOnForms(),
            DateTimeField::new('printUpdatedAt')->onlyOnDetail(),

            ChoiceField::new('type')->setChoices([
                'Entrée' => 'entree',
                'Plat' => 'plat',
                'Dessert' => 'dessert',
                'Sauce' => 'sauce'
            ]),
            ChoiceField::new('genre')->setChoices([
                'Poisson' => 'poisson',
                'Viande' => 'viande',
                'Autre' => 'autre',
            ]),
            AssociationField::new('category'),
            
            ImageField::new('imageName')->setBasePath('/recipes/images/recipes_images')->onlyOnDetail(),
            TextareaField::new('imageFile')->setFormType(VichImageType::class)->onlyOnForms(),
            DateTimeField::new('imageUpdatedAt')->onlyOnDetail(),
            TextareaField::new('altImage')->hideOnIndex(),
            
            AssociationField::new('times')->onlyOnDetail(),
            
            IntegerField::new('modulo'),
            TextField::new('unitModulo'),
            AssociationField::new('ingredients')->onlyOnDetail(),

            AssociationField::new('steps')->onlyOnDetail(),
            
            TextEditorField::new('info'),

            AssociationField::new('users')->onlyOnDetail()
        ];
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }
}
