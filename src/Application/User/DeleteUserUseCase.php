<?php
namespace App\Application\User;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class DeleteUserUseCase
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em
    ) {}

    public function execute(int $id): bool
    {
        $user = $this->userRepository->find($id);
        if (!$user) return false;

        $this->em->remove($user);
        $this->em->flush();

        return true;
    }
}