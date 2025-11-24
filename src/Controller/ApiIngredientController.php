<?php

namespace App\Controller;

use App\Repository\IngredientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ApiIngredientController extends AbstractController
{
    #[Route('/api/ingredients', name: 'api_ingredients', methods: ['GET'])]
    public function ingredients(IngredientRepository $ingredientRepository, SerializerInterface $serializer): JsonResponse
    {
        $ingredients = $ingredientRepository->findBy(['isIndexed' => true]);

        $context = [AbstractNormalizer::IGNORED_ATTRIBUTES => ['recipes', 'recipe', '__isCloning', 'imageUpdatedAt', 'isLiquid', 'imageFile', 'liquid', 'isIndexed']];

        $ingredientsJson = $serializer->serialize($ingredients, 'json', $context);

        $ingredientsArray = json_decode($ingredientsJson, true);

        foreach ($ingredientsArray as $key => $ingredient) {
            $imageName = $ingredient['imageName'];
            if ($imageName) {
                $ingredientsArray[$key]['image'] = 'https://backend.asyafood.fr/ingredients/images/ingredients_images/' . $imageName;
            }
        }

        usort($ingredientsArray, function ($a, $b) {
            return $a['name'] <=> $b['name'];
        });

        return $this->json($ingredientsArray);
    }
}
