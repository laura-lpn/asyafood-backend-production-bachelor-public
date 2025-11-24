<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ApiRegisterController extends AbstractController
{

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, SerializerInterface $serializer, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse
    {

        $data = $request->getContent();
        $user = $serializer->deserialize($data, User::class, 'json');
        // Create a new User instance and set the data from the request

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], 400);
        }

        if ($entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()])) {
            return new JsonResponse(['message' => 'User already exists'], 400);
        }
        if ($entityManager->getRepository(User::class)->findOneBy(['username' => $user->getUsername()])) {
            return new JsonResponse(['message' => 'Username already exists'], 400);
        }
        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

        $entityManager->persist($user);
        $entityManager->flush();

        // Save the user to the database
        return new JsonResponse(['message' => 'Registration successful']);
    }
}
