<?php

namespace App\Application\User;

use App\Repository\UserRepository;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UpdateUserUseCase
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em
    ) {}

    public function execute(int $id, array $data): ?User
    {
        $user = $this->userRepository->find($id);
        if (!$user) return null;

        if (isset($data['nom'])) $user->setNom($data['nom']);
        if (isset($data['prenom'])) $user->setPrenom($data['prenom']);
        if (isset($data['roles'])) $user->setRoles($data['roles']);

        $this->em->flush();
        return $user;
    }
}