<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ApiCategoryController extends AbstractController
{
    #[Route('/api/categories', name: 'api_categories', methods: ['GET'])]
    public function Recipes(CategoryRepository $CategoryRepository, SerializerInterface $serializer): JsonResponse
    {
        $Categories = $CategoryRepository->findAll();

        $context = [AbstractNormalizer::IGNORED_ATTRIBUTES => ['category', 'ingredients', 'times', 'users', 'steps', '__isCloning', 'genre', 'type', 'imageFile', 'printFile', 'modulo', 'unitModulo', 'motifUpdatedAt', 'motifFile', 'motifName', 'imageUpdatedAt', 'info', 'printUpdatedAt', 'users']];

        $CategoriesJson = $serializer->serialize($Categories, 'json', $context);

        $CategoriesArray = json_decode($CategoriesJson, true);

        foreach ($CategoriesArray as $key => $Category) {
            $imageName = $Category['imageName'];
            if ($imageName) {
                $CategoriesArray[$key]['image'] = 'https://backend.asyafood.fr/categories/images/categories_images/' . $imageName;
            }
        }

        foreach ($CategoriesArray as $key => $Category) {
            foreach ($Category['recipes'] as $key2 => $Recipe) {
                $imageName = $Recipe['imageName'];
                if ($imageName) {
                    $CategoriesArray[$key]['recipes'][$key2]['image'] = 'https://backend.asyafood.fr/recipes/images/recipes_images/' . $imageName;
                }
            }
        }

        return $this->json($CategoriesArray);
    }

    #[Route('/api/category/{slug}', name: 'api_category', methods: ['GET'])]
    public function Category(CategoryRepository $CategoryRepository, SerializerInterface $serializer, $slug): JsonResponse
    {
        $Category = $CategoryRepository->findOneby(['slug' => $slug]);

        if (!$Category) {
            return new JsonResponse(['message' => 'Category not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $context = [AbstractNormalizer::IGNORED_ATTRIBUTES => ['category', 'ingredients', 'times', 'users', 'steps', '__isCloning', 'imageFile', 'printFile', 'modulo', 'unitModulo', 'motifUpdatedAt', 'motifFile', 'imageUpdatedAt', 'info', 'printUpdatedAt', 'users', 'printName', 'copyright']];

        $CategoryJson = $serializer->serialize($Category, 'json', $context);

        $CategoryArray = json_decode($CategoryJson, true);

        $imageName = $Category->getImageName();
        if ($imageName) {
            $CategoryArray['image'] = 'https://backend.asyafood.fr/categories/images/categories_images/' . $imageName;
        }

        $motifName = $Category->getMotifName();
        if ($motifName) {
            $CategoryArray['motif'] = 'https://backend.asyafood.fr/categories/motifs/categories_motifs/'  . $motifName;
        }

        foreach ($CategoryArray['recipes'] as $key => $Recipe) {
            $imageName = $Recipe['imageName'];
            if ($imageName) {
                $CategoryArray['recipes'][$key]['image'] = 'https://backend.asyafood.fr/recipes/images/recipes_images/' . $imageName;
            }
        }

        return $this->json($CategoryArray);
    }
}
