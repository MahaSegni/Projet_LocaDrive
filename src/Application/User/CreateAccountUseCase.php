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
        // Validation des règles métier
        if (!preg_match('/^(?=(?:.[a-zA-Z]){4,})(?=(?:.\d){4,}).{8,}$/', $motDePasse)) {
            throw new \InvalidArgumentException("Le mot de passe doit contenir au moins 8 caractères avec 4 lettres et 4 chiffres.");
        }

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