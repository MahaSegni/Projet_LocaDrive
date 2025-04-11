<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Application\Enum\StatutReservation;

#[ORM\Entity]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", enumType: StatutReservation::class)]
    private StatutReservation $statut;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $dateCreation;

    #[ORM\Column(type: "string")]
    private string $modePaiement;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $client;

    #[ORM\OneToMany(mappedBy: "reservation", targetEntity: LigneReservation::class, cascade: ['persist', 'remove'])]
    private Collection $lignes;

    public function __construct(User $client, string $modePaiement)
    {
        $this->client = $client;
        $this->dateCreation = new \DateTimeImmutable();
        $this->modePaiement = $modePaiement;
        $this->statut = StatutReservation::EN_ATTENTE;
        $this->lignes = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStatut(): StatutReservation
    {
        return $this->statut;
    }

    public function setStatut(StatutReservation $statut): void
    {
        $this->statut = $statut;
    }

    public function getDateCreation(): \DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function getModePaiement(): string
    {
        return $this->modePaiement;
    }

    public function getClient(): User
    {
        return $this->client;
    }

    public function getLignes(): Collection
    {
        return $this->lignes;
    }

    public function addLigne(LigneReservation $ligne): void
    {
        $this->lignes->add($ligne);
        $ligne->setReservation($this);
    }
}