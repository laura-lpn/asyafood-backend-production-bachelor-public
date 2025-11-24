<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\ShoppingList;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ApiListController extends AbstractController
{
    #[Route('/api/list', name: 'api_list', methods: ['GET'])]
    public function getUserList(Request $request, JWTEncoderInterface $jwtEncoder, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $token = $request->cookies->get('ASYAFOOD');

        if (!$token) {
            return $this->json(['message' => 'Missing token'], 401);
        }

        try {
            $payload = $jwtEncoder->decode($token);
            $email = $payload['email'];

            $currentTimestamp = time();
            if (isset($payload['exp']) && $payload['exp'] < $currentTimestamp) {
                // Token has expired
                return $this->json(['message' => 'Token expired'], 401);
            }

            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            $list = $entityManager->getRepository(ShoppingList::class)->findBy(['user' => $user]);

        } catch (\Exception $e) {
            return $this->json(['message' => 'Invalid token'], 401);
        }

        $context = [AbstractNormalizer::IGNORED_ATTRIBUTES => ['recipes', 'user', '__isCloning'], AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,  AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT => 1];
        $ListJson = $serializer->serialize($list, 'json', $context);

        $ListArray = json_decode($ListJson, true);

        usort($ListArray, function ($a, $b) {
            return $a['ingredient']['name'] <=> $b['ingredient']['name'];
        });

        return $this->json($ListArray);
    }

    #[Route('/api/list/add', name: 'api_add_list', methods: ['POST'])]
    public function addList(Request $request, JWTEncoderInterface $jwtEncoder, EntityManagerInterface $entityManager): JsonResponse
    {
        $token = $request->cookies->get('ASYAFOOD');

        if (!$token) {
            return $this->json(['message' => 'Missing token'], 401);
        }
        try {
            $payload = $jwtEncoder->decode($token);
            $email = $payload['email'];

            $currentTimestamp = time();
            if (isset($payload['exp']) && $payload['exp'] < $currentTimestamp) {
                return $this->json(['message' => 'Token expired'], 401);
            }

            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Invalid token'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $ingredients = $data['ingredients'];
        $shoppingList = $user->getShoppingLists();

        foreach ($ingredients as $ingredient) {
            $existingIngredient = $shoppingList->filter(function ($entry) use ($ingredient) {
                return $entry->getIngredient()->getName() === $ingredient['name'];
            })->first();

            if ($existingIngredient) {
                $IngredientList = $entityManager->getRepository(Ingredient::class)->findOneByName($ingredient['name']);
                switch ($ingredient['unit']) {
                    case 'cl':
                        $existingIngredient->setQuantity($existingIngredient->getQuantity() + $ingredient['quantity'] * 10);
                        break;
                    case 'L':
                        $existingIngredient->setQuantity($existingIngredient->getQuantity() + $ingredient['quantity'] * 1000);
                        break;
                    case 'cuillerée à café':
                        if ($IngredientList->isLiquid()) {
                            $existingIngredient->setQuantity($existingIngredient->getQuantity() + $ingredient['quantity'] * 5);
                        } else {
                            $existingIngredient->setQuantity($existingIngredient->getQuantity() + $ingredient['quantity'] * 2);
                        }
                        break;
                    case 'cuillerée à soupe':
                        if ($IngredientList->isLiquid()) {
                            $existingIngredient->setQuantity($existingIngredient->getQuantity() + $ingredient['quantity'] * 15);
                        } else {
                            $existingIngredient->setQuantity($existingIngredient->getQuantity() + $ingredient['quantity']);
                        }
                        break;
                    default:
                        $existingIngredient->setQuantity($existingIngredient->getQuantity() + $ingredient['quantity']);
                        break;
                }
            } else {
                $newIngredient = new ShoppingList();
                $newIngredient->setIngredient($entityManager->getRepository(Ingredient::class)->findOneByName($ingredient['name']));
                $IngredientList = $entityManager->getRepository(Ingredient::class)->findOneByName($ingredient['name']);

                switch ($ingredient['unit']) {
                    case 'cl':
                        $newIngredient->setQuantity($ingredient['quantity'] * 10);
                        $newIngredient->setUnit('ml');
                        break;
                    case 'L':
                        $newIngredient->setQuantity($ingredient['quantity'] * 1000);
                        $newIngredient->setUnit('ml');
                        break;
                    case 'cuillerée à café':
                        if ($IngredientList->isLiquid()) {
                            $newIngredient->setQuantity($ingredient['quantity'] * 5);
                            $newIngredient->setUnit('ml');
                        } else {
                            $newIngredient->setQuantity($ingredient['quantity'] * 2);
                            $newIngredient->setUnit('cuillerée à soupe');
                        }
                        break;
                    case 'cuillerée à soupe':
                        if ($IngredientList->isLiquid()) {
                            $newIngredient->setQuantity($ingredient['quantity'] * 15);
                            $newIngredient->setUnit('ml');
                        } else {
                            $newIngredient->setQuantity($ingredient['quantity']);
                            $newIngredient->setUnit('cuillerée à soupe');
                        }
                        break;
                    default:
                        $newIngredient->setQuantity($ingredient['quantity']);
                        $newIngredient->setUnit($ingredient['unit']);
                        break;
                }
                $newIngredient->setUser($user);


                $entityManager->persist($newIngredient);
            }
        }

        $entityManager->flush();

        return $this->json(['message' => 'Ingredients added to shopping list'], 200);
    }
    #[Route('/api/list/validate/{id}', name: 'api_validate_list', methods: ['POST'])]
    public function validateListItem(EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $listItem = $entityManager->getRepository(ShoppingList::class)->findOneById($id);
        if (!$listItem) {
            return $this->json(['message' => 'List item not found'], 404);
        }
        // Mettez à jour la valeur "validate"
        $listItem->setValidate(!$listItem->isValidate());

        $entityManager->flush();

        return $this->json(['message' => 'List item validation status updated'], 200);
    }
    #[Route('/api/list/clear', name: 'api_clear_list', methods: ['POST'])]
    public function clearList(Request $request, JWTEncoderInterface $jwtEncoder, EntityManagerInterface $entityManager): JsonResponse
    {
        $token = $request->cookies->get('ASYAFOOD');

        if (!$token) {
            return $this->json(['message' => 'Missing token'], 401);
        }

        try {
            $payload = $jwtEncoder->decode($token);
            $email = $payload['email'];

            $currentTimestamp = time();
            if (isset($payload['exp']) && $payload['exp'] < $currentTimestamp) {
                // Token has expired
                return $this->json(['message' => 'Token expired'], 401);
            }

            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            $shoppingList = $user->getShoppingLists();

            foreach ($shoppingList as $item) {
                $entityManager->remove($item);
            }

            $entityManager->flush();

            return $this->json(['message' => 'Shopping list cleared'], 200);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Invalid token'], 401);
        }
        try {
            $user = // Obtenez l'utilisateur à partir du token
            $shoppingList = $user->getShoppingLists();

            foreach ($shoppingList as $item) {
                $entityManager->remove($item);
            }

            $entityManager->flush();

            return $this->json(['message' => 'Shopping list cleared'], 200);
        } catch (\Exception $e) {
            return $this->json(['message' => 'An error occurred'], 500);
        }
    }
}
