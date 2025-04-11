<?php
namespace App\Application\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateAccountUseCase
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function execute(
        string $email,
        string $motDePasse,
        string $nom,
        string $prenom,
        \DateTimeInterface $dateObtentionPermis
    ): void {
        if ($this->userRepository->existsByEmail($email)) {
            throw new \InvalidArgumentException("L'email est déjà utilisé.");
        }

        $user = new User(
            email: $email,
            nom: $nom,
            prenom: $prenom,
            dateObtentionPermis: $dateObtentionPermis
        );

        $motDePasseHashed = $this->passwordHasher->hashPassword($user, $motDePasse);
        $user->setPassword($motDePasseHashed);
        $user->setRoles(['ROLE_CLIENT']);

        $this->userRepository->save($user);
    }
}