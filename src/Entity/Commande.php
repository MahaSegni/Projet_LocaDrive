<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Application\Enum\StatutCommande;

#[ORM\Entity]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", enumType: StatutCommande::class)]
    private StatutCommande $statut;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $dateCreation;

    #[ORM\Column(type: "string")]
    private string $modePaiement;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $client;

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: "commande", cascade: ['persist', 'remove'])]
    private Collection $reservations;

    public function __construct(User $client, string $modePaiement)
    {
        $this->client = $client;
        $this->dateCreation = new \DateTimeImmutable();
        $this->modePaiement = $modePaiement;
        $this->statut = StatutCommande::CART;
        $this->reservations = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStatut(): StatutCommande
    {
        return $this->statut;
    }

    public function setStatut(StatutCommande $statut): void
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

    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): void
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setCommande($this);
        }
    }
    public function annuler(): void
    {
        if (!in_array($this->statut, [StatutCommande::CART, StatutCommande::EN_ATTENTE])) {
            throw new \LogicException("Cette commande ne peut pas être annulée.");
        }

        $this->statut = StatutCommande::ANNULEE;
    }
    public function confirmer(): void
    {
        if ($this->statut !== StatutCommande::CART) {
            throw new \LogicException("Seules les commandes en 'panier' peuvent être confirmées.");
        }

        $this->statut = StatutCommande::VALIDEE;
    }
    public function ajouterReservation(Vehicle $vehicule, \DateTimeImmutable $dateDebut, \DateTimeImmutable $dateFin): void
    {
        $reservation = new Reservation($vehicule, $dateDebut, $dateFin);
        $this->reservations->add($reservation);
    }
}