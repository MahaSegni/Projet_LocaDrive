<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class LoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_custom_login', methods: ['POST'])]
    public function login(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        JWTTokenManagerInterface $jwtManager
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';

            if (empty($email) || empty($password)) {
                return $this->json(['error' => 'Email and password are required'], 400);
            }

            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user || !$hasher->isPasswordValid($user, $password)) {
                return $this->json(['error' => 'Invalid credentials'], 401);
            }

            $token = $jwtManager->create($user);

            return $this->json([
                'token' => $token,
                'email' => $user->getUserIdentifier(),
                'roles' => $user->getRoles()
            ]);
        } catch (\Doctrine\ORM\OptimisticLockException | \Doctrine\ORM\PessimisticLockException $e) {
            return $this->json(['error' => 'Database error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return $this->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}