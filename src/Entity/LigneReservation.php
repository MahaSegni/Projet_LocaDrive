<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class LigneReservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $dateDebut;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $dateFin;

    #[ORM\ManyToOne(targetEntity: Reservation::class, inversedBy: "lignes")]
    #[ORM\JoinColumn(nullable: false)]
    private Reservation $reservation;

    #[ORM\ManyToOne(targetEntity: Vehicle::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Vehicle $vehicule;

    public function __construct(Vehicle $vehicule, \DateTimeInterface $dateDebut, \DateTimeInterface $dateFin)
    {
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

    public function getReservation(): Reservation
    {
        return $this->reservation;
    }

    public function setReservation(Reservation $reservation): void
    {
        $this->reservation = $reservation;
    }
}