<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry,private EntityManagerInterface $em)
    {
        parent::__construct($registry, User::class);
        $this->em = $em;
    }


    /**
     * Rehash user password over time (used by Symfony security).
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * Save or update a user
     */
    public function save(User $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * Delete a user
     */
    public function delete(User $user): void
    {
        $this->em->remove($user);
        $this->em->flush();
    }

    /**
     * Find user by ID
     */
    public function findById(int $id): ?User
    {
        return $this->find($id);
    }

    /**
     * Check if user exists by email
     */
    public function existsByEmail(string $email): bool
    {
        return (bool) $this->findOneBy(['email' => $email]);
    }
}