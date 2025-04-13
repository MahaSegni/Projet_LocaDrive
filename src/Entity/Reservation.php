<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Application\Enum\StatutCommande;

#[ORM\Entity]
class Reservation
{
    // Prix fixe de l'assurance en euros
    private const INSURANCE_PRICE = 20.0;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $dateDebut;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $dateFin;

    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: "reservations")]
    #[ORM\JoinColumn(nullable: false)]
    private Commande $commande;

    #[ORM\ManyToOne(targetEntity: Vehicle::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Vehicle $vehicule;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $price = null;

    #[ORM\Column(type: "boolean")]
    private bool $hasInsurance = false;

    public function __construct(Vehicle $vehicule, \DateTimeInterface $dateDebut, \DateTimeInterface $dateFin)
    {
        if ($dateFin < $dateDebut) {
            throw new \InvalidArgumentException("La date de fin doit être postérieure à la date de début.");
        }

        $now = new \DateTimeImmutable();
        if ($dateDebut < $now) {
            throw new \InvalidArgumentException("La date de début doit être dans le futur.");
        }

        $this->vehicule = $vehicule;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->price = $this->calculatePrice();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDateDebut(): \DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function getDateFin(): \DateTimeInterface
    {
        return $this->dateFin;
    }

    public function getVehicule(): Vehicle
    {
        return $this->vehicule;
    }

    public function getCommande(): Commande
    {
        return $this->commande;
    }

    public function setCommande(Commande $commande): void
    {
        $this->commande = $commande;
    }

    /**
     * Calcul du prix basé sur le tarif journalier et la durée
     */
    public function calculatePrice(): float
    {
        $days = $this->dateDebut->diff($this->dateFin)->days + 1; // +1 car on compte le jour de début
        return $this->vehicule->getDailyRate() * $days;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * Ajoute une assurance à la réservation
     * @throws \LogicException si la commande n'est pas en statut panier
     */
    public function ajouterAssurance(): void
    {
        if ($this->commande->getStatut() !== StatutCommande::CART) {
            throw new \LogicException("On ne peut ajouter une assurance que sur une commande en statut panier.");
        }

        if ($this->hasInsurance) {
            throw new \LogicException("Cette réservation a déjà une assurance.");
        }

        $this->hasInsurance = true;
    }

    /**
     * Retire l'assurance de la réservation
     * @throws \LogicException si la commande n'est pas en statut panier
     */
    public function removeInsurance(): void
    {
        if ($this->commande->getStatut() !== StatutCommande::CART) {
            throw new \LogicException("On ne peut retirer une assurance que sur une commande en statut panier.");
        }

        if (!$this->hasInsurance) {
            throw new \LogicException("Cette réservation n'a pas d'assurance.");
        }

        $this->hasInsurance = false;
    }

    /**
     * Indique si la réservation possède une assurance
     */
    public function hasInsurance(): bool
    {
        return $this->hasInsurance;
    }

    /**
     * Retourne le prix de l'assurance (0 si pas d'assurance)
     */
    public function getInsurancePrice(): float
    {
        return $this->hasInsurance ? self::INSURANCE_PRICE : 0;
    }

    /**
     * Retourne le prix total de la réservation (prix de base + assurance si applicable)
     */
    public function getTotalPrice(): float
    {
        return $this->price + $this->getInsurancePrice();
    }
}