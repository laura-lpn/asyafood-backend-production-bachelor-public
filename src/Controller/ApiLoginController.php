<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ApiLoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, UserPasswordHasherInterface $passwordEncoder, UserRepository $UserRepository, JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return new JsonResponse(['message' => 'Missing email or password'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $UserRepository->findOneBy(['email' => $email]);

        if (!$user || !$passwordEncoder->isPasswordValid($user, $password)) {
            return new JsonResponse(['message' => 'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $token = $JWTManager->create($user);

        $response = new JsonResponse([
            'token' => $token,
            'user' => $user->getUsername(),
        ]);

        $response->headers->setCookie(
            Cookie::create('ASYAFOOD')
                ->withValue($token)
                ->withExpires(time() + 3600)
                ->withPath('/')
                ->withDomain($request->getHost())
                ->withSecure(true)
                ->withHttpOnly(true)
                ->withSameSite('none')
        );

        return $response;
    }
    #[Route('/api/logout', name: 'api_logout', methods: ['GET'])]
    public function logout(): Response
    {
        $response = $this->json(['message' => 'Logout']);
        $response->headers->clearCookie(
            'ASYAFOOD',
            '/',
            '.backend.asyafood.fr',
            true,
            true,
            'none'
        );
        return $response;
    }
}
