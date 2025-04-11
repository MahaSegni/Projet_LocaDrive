<?php
namespace App\Application\User;

use App\Repository\UserRepository;

class ListUsersUseCase
{
    public function __construct(private UserRepository $userRepository) {}

    public function execute(): array
    {
        return $this->userRepository->findAll();
       }
}