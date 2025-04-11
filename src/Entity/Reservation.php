<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Reservation
{
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

    public function __construct(Vehicle $vehicule, \DateTimeInterface $dateDebut, \DateTimeInterface $dateFin)
    {
        if ($dateFin < $dateDebut) {
            throw new \InvalidArgumentException("La date de fin doit être postérieure à la date de début.");
        }

        $this->vehicule = $vehicule;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
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
}