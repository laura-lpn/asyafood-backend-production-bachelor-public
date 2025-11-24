<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Category;
use App\Entity\User;
use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\IngredientRecipe;
use App\Entity\TimeType;
use App\Entity\TimeRecipe;
use App\Entity\Step;
use App\Entity\ShoppingList;
use App\Controller\Admin\RecipeCrudController;
use App\Entity\Unit;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator
            ->setController(RecipeCrudController::class)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Backend')
            ->disableDarkMode()
            ->setFaviconPath('img/favicon-rouge.svg');
    }
    
    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)

            ->displayUserAvatar(false);
    }
    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addWebpackEncoreEntry('admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Categories');

        yield MenuItem::subMenu('Categories', 'fas fa-earth-americas')->setSubItems([
            MenuItem::linkToCrud('add', 'fas fa-plus', Category::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('show', 'fas fa-eye', Category::class)
        ]);
        
        yield MenuItem::section('Ingredients');

        yield MenuItem::subMenu('Ingredients', 'fas fa-pepper-hot')->setSubItems([
            MenuItem::linkToCrud('add', 'fas fa-plus', Ingredient::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('show', 'fas fa-eye', Ingredient::class),
        ]);
        yield MenuItem::subMenu('Units', 'fas fa-ruler-horizontal')->setSubItems([
            MenuItem::linkToCrud('add', 'fas fa-plus', Unit::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('show', 'fas fa-eye', Unit::class),
        ]);

        yield MenuItem::section('Times');

        yield MenuItem::subMenu('TimeTypes', 'fas fa-clock')->setSubItems([
            MenuItem::linkToCrud('add', 'fas fa-plus', TimeType::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('show', 'fas fa-eye', TimeType::class),
        ]);
        
        yield MenuItem::section('Recipes');

        yield MenuItem::subMenu('recipes', 'fas fa-spoon') ->setSubItems([
            MenuItem::linkToCrud('add', 'fas fa-plus', Recipe::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('show', 'fas fa-eye', Recipe::class)
        ]);
        yield MenuItem::subMenu('ingredientRecipes', 'fas fa-jar')->setSubItems([
            MenuItem::linkToCrud('add', 'fas fa-plus', ingredientRecipe::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('show', 'fas fa-eye', ingredientRecipe::class),
        ]);
        yield MenuItem::subMenu('TimeRecipes', 'fas fa-hourglass-half')->setSubItems([
            MenuItem::linkToCrud('add', 'fas fa-plus', TimeRecipe::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('show', 'fas fa-eye', TimeRecipe::class),
        ]);
        yield MenuItem::subMenu('Steps', 'fas fa-layer-group')->setSubItems([
            MenuItem::linkToCrud('add', 'fas fa-plus', Step::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('show', 'fas fa-eye', Step::class),
        ]);
        
        yield MenuItem::section('Users');

        yield MenuItem::subMenu('ShoppingLists', 'fas fa-list')->setSubItems([
            MenuItem::linkToCrud('add', 'fas fa-plus', ShoppingList::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('show', 'fas fa-eye', ShoppingList::class),
        ]);
        yield MenuItem::subMenu('Users', 'fas fa-user')->setSubItems([
            MenuItem::linkToCrud('add', 'fas fa-plus', User::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('show', 'fas fa-eye', User::class),
        ]);
    }
}
