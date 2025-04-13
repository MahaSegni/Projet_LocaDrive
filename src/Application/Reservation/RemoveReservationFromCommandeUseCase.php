<?php

namespace App\Application\Reservation;

use App\Entity\Commande;
use App\Entity\User;
use App\Repository\CommandeRepository;
use App\Repository\ReservationRepository;

class RemoveReservationFromCommandeUseCase
{
    private CommandeRepository $commandeRepository;
    private ReservationRepository $reservationRepository;

    public function __construct(
        CommandeRepository $commandeRepository,
        ReservationRepository $reservationRepository
    ) {
        $this->commandeRepository = $commandeRepository;
        $this->reservationRepository = $reservationRepository;
    }

    /**
     * Retire une réservation d'une commande
     */
    public function execute(User $client, int $commandeId, int $reservationId): Commande
    {
        try {
            $commande = $this->commandeRepository->findById($commandeId);

            if (!$commande) {
                throw new \InvalidArgumentException("Commande introuvable.");
            }

            $commande->verifierClient($client);

            $reservation = $this->reservationRepository->findById($reservationId);

            if (!$reservation) {
                throw new \InvalidArgumentException("Réservation introuvable.");
            }

            $commande->retirerReservation($reservation);

            $this->commandeRepository->save($commande);

            return $commande;

        } catch (\InvalidArgumentException $e) {
            throw $e;

        } catch (\Throwable $e) {
            throw new \RuntimeException("Une erreur est survenue lors de la suppression de la réservation.");
        }
    }
}