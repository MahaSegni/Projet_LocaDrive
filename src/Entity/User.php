<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: "`user`")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 180, unique: true)]
    private string $email;

    #[ORM\Column(type: "string")]
    private string $motDePasse;

    #[ORM\Column(type: "string")]
    private string $nom;

    #[ORM\Column(type: "string")]
    private string $prenom;

    #[ORM\Column(type: "date", nullable: true)]
    private ?\DateTimeInterface $dateObtentionPermis = null;

    #[ORM\Column(type: "json")]
    private array $roles = [];

    public function __construct(
        string $email,
        string $nom,
        string $prenom,
        ?\DateTimeInterface $dateObtentionPermis = null,
        array $roles = ['ROLE_USER']
    ) {
        $this->setEmail($email);
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->dateObtentionPermis = $dateObtentionPermis;
        $this->roles = $roles;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        // On pourrait ajouter une validation d'email ici si nécessaire
        $this->email = $email;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->motDePasse;
    }

    public function setPassword(string $motDePasse): void
    {
        // Si le mot de passe n'est pas déjà hashé, on le valide
        if (substr($motDePasse, 0, 1) !== '$') {
            $this->validateMotDePasse($motDePasse);
        }

        $this->motDePasse = $motDePasse;
    }

    /**
     * Valide le format du mot de passe selon les règles métier
     */
    private function validateMotDePasse(string $motDePasse): void
    {
        if (!preg_match('/^(?=(?:.[a-zA-Z]){4,})(?=(?:.\d){4,}).{8,}$/', $motDePasse)) {
            throw new \InvalidArgumentException("Le mot de passe doit contenir au moins 8 caractères avec 4 lettres et 4 chiffres.");
        }
    }

    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function eraseCredentials(): void
    {
        // Aucun champ sensible temporaire à effacer
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function getDateObtentionPermis(): ?\DateTimeInterface
    {
        return $this->dateObtentionPermis;
    }

    public function setDateObtentionPermis(?\DateTimeInterface $date): void
    {
        $this->dateObtentionPermis = $date;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}