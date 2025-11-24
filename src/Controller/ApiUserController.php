<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ApiUserController extends AbstractController
{
    #[Route('/api/check-token', name: 'api_token', methods: ['GET'])]
    public function getToken(Request $request, JWTEncoderInterface $jwtEncoder, EntityManagerInterface $entityManager): JsonResponse
    {
        $token = $request->cookies->get('ASYAFOOD');

        if (!$token) {
            // Token is missing
            return $this->json(['message' => 'Missing token'], 401);
        }
        // Decode the token
        try {
            $payload = $jwtEncoder->decode($token);
            $email = $payload['email'];
            
            // Check if the token is expired
            $currentTimestamp = time();
            if (isset($payload['exp']) && $payload['exp'] < $currentTimestamp) {
                // Token has expired
                return $this->json(['message' => 'Token expired'], 401);
            }

            // Get user with the email and see if the user is valid
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user) {
                // User not found or invalid
                return $this->json(['message' => 'Invalid user'], 401);
            }
        } catch (\Exception $e) {
            // Token is invalid or cannot be decoded
            var_dump($e->getMessage());
            return $this->json(['message' => 'Invalid token'], 401);
        }

        return $this->json(['valid' => true, 'message' => 'Token is valid']);
    }
    #[Route('/api/profil', name: 'api_profil', methods: ['GET'])]
    public function getUserProfile(Request $request, JWTEncoderInterface $jwtEncoder, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $token = $request->cookies->get('ASYAFOOD');

        if (!$token) {
            // Token is missing
            return $this->json(['message' => 'Missing token'], 401);
        }

        // Decode the token
        try {
            $payload = $jwtEncoder->decode($token);
            $email = $payload['email'];

            $currentTimestamp = time();
            if (isset($payload['exp']) && $payload['exp'] < $currentTimestamp) {
                // Token has expired
                return $this->json(['message' => 'Token expired'], 401);
            }

            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user) {
                return $this->json(['message' => 'Invalid user'], 401);
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            return $this->json(['message' => 'Invalid token'], 401);
        }

        $context = [AbstractNormalizer::IGNORED_ATTRIBUTES => ['shoppingLists', 'password', 'createdAt', 'roles', 'userIdentifier', 'id', 'resetPasswordRequests', 'users', '__isCloning'], AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,  AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT => 1];
        $UserJson = $serializer->serialize($user, 'json', $context);

        $UserArray = json_decode($UserJson, true);

        return $this->json($UserArray);
    }
    #[Route('/api/profil/edit-email', name: 'api_edit_email_profil', methods: ['POST'])]
    public function editEmail(Request $request, JWTEncoderInterface $jwtEncoder, EntityManagerInterface $entityManager): JsonResponse
    {
        $token = $request->cookies->get('ASYAFOOD');

        if (!$token) {
            // Token is missing
            return $this->json(['message' => 'Missing token'], 401);
        }

        // Decode the token
        try {
            $payload = $jwtEncoder->decode($token);
            $email = $payload['email'];

            $currentTimestamp = time();
            if (isset($payload['exp']) && $payload['exp'] < $currentTimestamp) {
                // Token has expired
                return $this->json(['message' => 'Token expired'], 401);
            }

            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user) {
                return $this->json(['message' => 'Invalid user'], 401);
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            return $this->json(['message' => 'Invalid token'], 401);
        }

        $data = json_decode($request->getContent(), true);

        if ($entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']])) {
            return $this->json(['message' => 'Email already exists'], 409);
        }

        $user->setEmail($data['email']);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(['message' => 'Email updated']);
    }
    #[Route('/api/profil/edit-password', name: 'api_edit_password_profil', methods: ['POST'])]
    public function editPassword(Request $request, UserPasswordHasherInterface $passwordHasher, JWTEncoderInterface $jwtEncoder, EntityManagerInterface $entityManager): JsonResponse
    {
        $token = $request->cookies->get('ASYAFOOD');

        if (!$token) {
            // Token is missing
            return $this->json(['message' => 'Missing token'], 401);
        }

        // Decode the token
        try {
            $payload = $jwtEncoder->decode($token);
            $email = $payload['email'];

            $currentTimestamp = time();
            if (isset($payload['exp']) && $payload['exp'] < $currentTimestamp) {
                // Token has expired
                return $this->json(['message' => 'Token expired'], 401);
            }

            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user) {
                return $this->json(['message' => 'Invalid user'], 401);
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            return $this->json(['message' => 'Invalid token'], 401);
        }

        $data = json_decode($request->getContent(), true);

        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(['message' => 'Password updated']);
    }
    #[Route('/api/profil/edit_username', name: 'api_edit_username_profil', methods: ['POST'])]
    public function editUsername(Request $request, JWTEncoderInterface $jwtEncoder, EntityManagerInterface $entityManager): JsonResponse
    {
        $token = $request->cookies->get('ASYAFOOD');

        if (!$token) {
            // Token is missing
            return $this->json(['message' => 'Missing token'], 401);
        }

        // Decode the token
        try {
            $payload = $jwtEncoder->decode($token);
            $email = $payload['email'];

            $currentTimestamp = time();
            if (isset($payload['exp']) && $payload['exp'] < $currentTimestamp) {
                // Token has expired
                return $this->json(['message' => 'Token expired'], 401);
            }

            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user) {
                return $this->json(['message' => 'Invalid user'], 401);
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            return $this->json(['message' => 'Invalid token'], 401);
        }

        $data = json_decode($request->getContent(), true);

        if ($entityManager->getRepository(User::class)->findOneBy(['username' => $data['username']])) {
            return $this->json(['message' => 'Username already exists'], 409);
        }

        $user->setUsername($data['username']);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(['message' => 'Username updated']);
    }
    #[Route('/api/profil/delete', name: 'api_delete_profil', methods: ['POST'])]
    public function deleteProfil(Request $request, JWTEncoderInterface $jwtEncoder, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): JsonResponse
    {
        $token = $request->cookies->get('ASYAFOOD');

        $password = json_decode($request->getContent(), true)['password'];

        if (!$token) {
            // Token is missing
            return $this->json(['message' => 'Missing token'], 401);
        }

        // Decode the token
        try {
            $payload = $jwtEncoder->decode($token);
            $email = $payload['email'];

            $currentTimestamp = time();
            if (isset($payload['exp']) && $payload['exp'] < $currentTimestamp) {
                // Token has expired
                return $this->json(['message' => 'Token expired'], 401);
            }

            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user) {
                return $this->json(['message' => 'Invalid user'], 401);
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            return $this->json(['message' => 'Invalid token'], 401);
        };
        if (!$password) {
            return $this->json(['message' => 'Missing password'], 401);
        }

        if (!$passwordHasher->isPasswordValid($user, $password)) {
            return $this->json(['message' => 'Invalid password'], 401);
        }
        $entityManager->remove($user);
        $entityManager->flush();
        
        //remove token
        $response = new JsonResponse(['message' => 'User deleted']);
        $response->headers->clearCookie('ASYAFOOD');

        return $response;
    }
}