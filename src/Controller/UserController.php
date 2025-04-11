<?php
namespace App\Controller;

use App\Application\User\CreateAccountUseCase;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/api/account', name: 'create_account', methods: ['POST'])]
    public function create_account(Request $request, CreateAccountUseCase $useCase): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $useCase->execute(
                $data['email'] ?? '',
                $data['motDePasse'] ?? '',
                $data['nom'] ?? '',
                $data['prenom'] ?? '',
                new DateTimeImmutable($data['dateObtentionPermis'] ?? 'now')
            );

            return $this->json(['message' => 'Compte créé avec succès'], 201);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['erreur' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return $this->json(['erreur' => 'Erreur serveur'], 500);
        }
    }


    #[Route('/create-admin', name: 'create_admin', methods: ['GET'])]
    public function createAdmin(UserPasswordHasherInterface $hasher, EntityManagerInterface $em): JsonResponse
    {
        $admin = new User(
            email: 'admin2@example.com',
            nom: 'Admin',
            prenom: 'Boss',
            dateObtentionPermis: new \DateTimeImmutable('2000-01-01')
        );

        $hashedPassword = $hasher->hashPassword($admin, 'Admin1234');
        $admin->setPassword($hashedPassword);
        $admin->setRoles(['ROLE_ADMIN']);

        $em->persist($admin);
        $em->flush();

        return $this->json(['message' => 'Admin created', 'email' => $admin->getEmail()]);
    }


    #[Route('/api/profile', name: 'get_profile', methods: ['GET'])]
    public function getProfile(): JsonResponse
    {
        $user = $this->getUser();

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'datePermis' => $user->getDateObtentionPermis()->format('Y-m-d'),
            'roles' => $user->getRoles()
        ]);
    }

    #[Route('/api/profile', name: 'update_profile', methods: ['PUT'])]
    public function updateProfile(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (isset($data['nom'])) $user->setNom($data['nom']);
        if (isset($data['prenom'])) $user->setPrenom($data['prenom']);
        if (isset($data['datePermis'])) {
            $user->setDateObtentionPermis(new \DateTimeImmutable($data['datePermis']));
        }

        $em->flush();
        return $this->json(['message' => 'Profile updated']);
    }


    #[Route('/api/admin/users', name: 'admin_list_users', methods: ['GET'])]
    public function listUsers(EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $users = $em->getRepository(User::class)->findAll();

        $data = array_map(fn($u) => [
            'id' => $u->getId(),
            'email' => $u->getEmail(),
            'nom' => $u->getNom(),
            'prenom' => $u->getPrenom(),
            'roles' => $u->getRoles()
        ], $users);

        return $this->json($data);
    }

    #[Route('/api/admin/users/{id}', name: 'admin_get_user', methods: ['GET'])]
    public function getUserById(User $user): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'roles' => $user->getRoles()
        ]);
    }

    #[Route('/api/admin/users/{id}', name: 'admin_update_user', methods: ['PUT'])]
    public function updateUser(User $user, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $data = json_decode($request->getContent(), true);

        if (isset($data['nom'])) $user->setNom($data['nom']);
        if (isset($data['prenom'])) $user->setPrenom($data['prenom']);
        if (isset($data['roles'])) $user->setRoles($data['roles']);

        $em->flush();
        return $this->json(['message' => 'User updated']);
    }

    #[Route('/api/admin/users/{id}', name: 'admin_delete_user', methods: ['DELETE'])]
    public function deleteUser(User $user, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em->remove($user);
        $em->flush();

        return $this->json(['message' => 'User deleted']);
    }

}