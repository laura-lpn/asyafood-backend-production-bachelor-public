<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class ApiRecipeController extends AbstractController
{
    #[Route('/api/recipes', name: 'api_recipes', methods: ['GET'])]
    public function Recipes(Request $request, RecipeRepository $RecipeRepository, SerializerInterface $serializer): JsonResponse
    {
        $limit = $request->query->getInt('limit', 10);
        $orderBy = $request->query->get('orderBy', 'createdAt');
        $order = $request->query->get('order', 'desc');

        if (!in_array($order, ['asc', 'desc'])) {
            return $this->json(['error' => 'Invalid order value'], Response::HTTP_BAD_REQUEST);
        }

        $Recipes = $RecipeRepository->findBy([], [$orderBy => $order], $limit);

        $context = [AbstractNormalizer::IGNORED_ATTRIBUTES => ['recipes', 'recipe', '__isCloning', 'imageFile', 'imageUpdatedAt', 'printFile', 'printName', 'printUpdatedAt', 'isLiquid', 'liquid', 'metaDescription', 'ingredients', 'times', 'steps', 'users', 'print']];
        $RecipesJson = $serializer->serialize($Recipes, 'json', $context);

        $RecipesArray = json_decode($RecipesJson, true);

        foreach ($RecipesArray as $key => $Recipe) {
            $imageName = $Recipe['imageName'];
            if ($imageName) {
                $RecipesArray[$key]['image'] = 'https://backend.asyafood.fr/recipes/images/recipes_images/' . $imageName;
            }
        }

        return $this->json($RecipesArray);
    }
    #[Route('/api/recipe/{slug}', name: 'api_recipe', methods: ['GET'])]
    public function Recipe(RecipeRepository $RecipeRepository, SerializerInterface $serializer, $slug): JsonResponse
    {
        $Recipe = $RecipeRepository->findOneby(['slug' => $slug]);

        if (!$Recipe) {
            throw $this->createNotFoundException('Recipe not found');
        }

        $context = [AbstractNormalizer::IGNORED_ATTRIBUTES => ['recipes', 'recipe', '__isCloning', 'imageFile', 'imageUpdatedAt', 'printFile', 'printUpdatedAt', 'isLiquid', 'liquid', 'category', 'users']];

        $RecipeJson = $serializer->serialize($Recipe, 'json', $context);

        $RecipeArray = json_decode($RecipeJson, true);

        $imageName = $Recipe->getImageName();
        if ($imageName) {
            $RecipeArray['image'] = 'https://backend.asyafood.fr/recipes/images/recipes_images/' . $imageName;
        }

        $printName = $Recipe->getPrintName();
        if ($printName) {
            $RecipeArray['print'] = 'https://backend.asyafood.fr/recipes/pdf/recipes_prints/' . $printName;
        }

        return $this->json($RecipeArray);
    }
}
