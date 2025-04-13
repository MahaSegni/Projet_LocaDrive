<?php

namespace App\Application\Reservation;

use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\CommandeRepository;
use App\Repository\VehicleRepository;
use DateTimeImmutable;

class AddReservationToCommandeUseCase
{
    private CommandeRepository $commandeRepository;
    private VehicleRepository $vehicleRepository;

    public function __construct(
        CommandeRepository $commandeRepository,
        VehicleRepository  $vehicleRepository
    )
    {
        $this->commandeRepository = $commandeRepository;
        $this->vehicleRepository = $vehicleRepository;
    }

    /**
     * Ajoute une réservation à une commande existante
     *
     * @param User $client Le client qui fait la demande
     * @param int $commandeId L'identifiant de la commande
     * @param int $vehiculeId L'identifiant du véhicule à réserver
     * @param string $dateDebut La date de début au format Y-m-d
     * @param string $dateFin La date de fin au format Y-m-d
     * @return Reservation La réservation créée
     * @throws \InvalidArgumentException Si les paramètres sont invalides
     */
    public function execute(User $client, int $commandeId, int $vehiculeId, string $dateDebut, string $dateFin): Reservation
    {
        try {
            $commande = $this->commandeRepository->findById($commandeId);

            if (!$commande) {
                throw new \InvalidArgumentException("Commande introuvable.");
            }

            $commande->verifierClient($client);

            $vehicule = $this->vehicleRepository->findById($vehiculeId);

            if (!$vehicule) {
                throw new \InvalidArgumentException("Véhicule introuvable.");
            }

            try {
                $dateDebutObj = new DateTimeImmutable($dateDebut);
                $dateFinObj = new DateTimeImmutable($dateFin);
            } catch (\Exception $e) {
                throw new \InvalidArgumentException("Format de date invalide.");
            }

            $reservation = $commande->ajouterReservation($vehicule, $dateDebutObj, $dateFinObj);

            $this->commandeRepository->save($commande);

            return $reservation;

        } catch (\InvalidArgumentException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new \RuntimeException("Une erreur est survenue lors de l'ajout de la réservation.");
        }
    }
}