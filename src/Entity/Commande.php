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

    #[ORM\OneToMany(mappedBy: "commande", targetEntity: Reservation::class, cascade: ['persist', 'remove'])]
    private Collection $reservations;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $prixTotal = null;

    public function __construct(User $client, string $modePaiement)
    {
        $this->client = $client;
        $this->dateCreation = new \DateTimeImmutable();
        $this->modePaiement = $modePaiement;
        $this->statut = StatutCommande::CART;
        $this->reservations = new ArrayCollection();
        $this->prixTotal = 0.0;
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

    public function setModePaiement(string $modePaiement): void
    {
        if ($this->statut !== StatutCommande::CART) {
            throw new \LogicException("On ne peut modifier le mode de paiement que sur une commande en statut panier.");
        }

        // Validation du mode de paiement
        if (!in_array($modePaiement, ['CB', 'PayPal'])) {
            throw new \InvalidArgumentException("Mode de paiement non supporté.");
        }

        $this->modePaiement = $modePaiement;
    }

    public function getClient(): User
    {
        return $this->client;
    }

    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    /**
     * Méthode interne pour recalculer le prix total
     */
    private function recalculateTotalPrice(): void
    {
        $this->prixTotal = 0;
        foreach ($this->reservations as $reservation) {
            $this->prixTotal += $reservation->getTotalPrice();
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

        if ($this->reservations->isEmpty()) {
            throw new \LogicException("Impossible de confirmer une commande sans réservation.");
        }

        $this->statut = StatutCommande::VALIDEE;
        $this->recalculateTotalPrice();
    }

    /**
     * Méthode riche pour ajouter une réservation avec validation des règles métier
     */
    public function ajouterReservation(Vehicle $vehicule, \DateTimeInterface $dateDebut, \DateTimeInterface $dateFin): Reservation
    {
        if ($this->statut !== StatutCommande::CART) {
            throw new \LogicException("On ne peut ajouter une réservation que sur une commande en statut panier.");
        }

        $now = new \DateTimeImmutable();
        if ($dateDebut < $now) {
            throw new \InvalidArgumentException("La date de début doit être dans le futur.");
        }

        if ($dateFin < $dateDebut) {
            throw new \InvalidArgumentException("La date de fin doit être après la date de début.");
        }

        $reservation = new Reservation($vehicule, $dateDebut, $dateFin);
        $this->reservations->add($reservation);
        $reservation->setCommande($this);

        $this->recalculateTotalPrice();

        return $reservation;
    }

    /**
     * Méthode riche pour retirer une réservation avec validation des règles métier
     */
    public function retirerReservation(Reservation $reservation): void
    {
        // Vérifier que la commande est en statut panier
        if ($this->statut !== StatutCommande::CART) {
            throw new \LogicException("On ne peut retirer une réservation que d'une commande en statut panier.");
        }

        // Vérifier que la réservation appartient à cette commande
        if (!$this->reservations->contains($reservation)) {
            throw new \InvalidArgumentException("Cette réservation n'appartient pas à cette commande.");
        }

        // Retrait de la réservation
        $this->reservations->removeElement($reservation);

        // Mise à jour du prix total
        $this->recalculateTotalPrice();
    }

    /**
     * Méthode pour calculer et retourner le prix total de la commande
     */
    public function getTotalPrice(): float
    {
        $this->recalculateTotalPrice();
        return $this->prixTotal;
    }

    /**
     * Retourne le prix total stocké (pour les commandes confirmées)
     */
    public function getPrixTotal(): float
    {
        return $this->prixTotal ?? 0.0;
    }

    /**
     * Méthode pour notifier le changement de prix d'une réservation
     * (appelée par une réservation quand son prix change)
     */
    public function notifierChangementPrixReservation(): void
    {
        $this->recalculateTotalPrice();
    }
    public function verifierClient(User $client): void
    {
        if ($this->getClient()->getId() !== $client->getId()) {
            throw new \InvalidArgumentException("Cette commande ne vous appartient pas.");
        }
    }

    public function recupererReservationOuEchouer(int $reservationId): Reservation
    {
        foreach ($this->getReservations() as $res) {
            if ($res->getId() === $reservationId) {
                return $res;
            }
        }

        throw new \InvalidArgumentException("Réservation introuvable dans cette commande.");
    }

}