<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            SlugField::new('slug', 'Slug')->setTargetFieldName('name')->hideOnIndex(),
            ColorField::new('color'),
            TextEditorField::new('description'),
            TextareaField::new('metaDescription'),
            
            ImageField::new('motifName')->setBasePath('/categories/images/categories_motifs')->onlyOnDetail(),
            TextareaField::new('motifFile')->setFormType(VichImageType::class)->onlyOnForms(),
            DateTimeField::new('motifUpdatedAt')->onlyOnDetail(),
            TextareaField::new('altMotif')->hideOnIndex(),
            
            ImageField::new('imageName')->setBasePath('/categories/images/categories_images')->onlyOnDetail(),
            TextareaField::new('imageFile')->setFormType(VichImageType::class)->onlyOnForms(),
            DateTimeField::new('imageUpdatedAt')->onlyOnDetail(),
            TextareaField::new('altImage')->hideOnIndex(),
            TextEditorField::new('copyright')->hideOnIndex(),

            AssociationField::new('recipes')
        ];
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }
}
