<?php
namespace App\Application\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateAccountUseCase
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

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

        $user = new User($email, $nom, $prenom, $dateObtentionPermis);
        $user->initialiserMotDePasse($this->passwordHasher, $motDePasse);

        $this->userRepository->save($user);
    }
}
