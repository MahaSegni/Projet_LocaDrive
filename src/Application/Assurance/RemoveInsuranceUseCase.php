<?php

namespace App\Application\Assurance;

use App\Entity\User;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

class RemoveInsuranceUseCase
{
    public function __construct(
        private CommandeRepository $commandeRepository,
        private EntityManagerInterface $entityManager
    ) {
        $this->commandeRepository = $commandeRepository;
        $this->entityManager = $entityManager;
    }

    public function execute(User $client, int $commandeId, int $reservationId): float
    {
        try {
            $commande = $this->commandeRepository->findById($commandeId);

            if (!$commande) {
                throw new InvalidArgumentException("Commande introuvable.");
            }

            $commande->verifierClient($client);

            $reservation = $commande->recupererReservationOuEchouer($reservationId);

            $reservation->removeInsurance();

            $commande->notifierChangementPrixReservation();

            $this->entityManager->flush();

            return $commande->getTotalPrice();
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new \RuntimeException("Erreur lors de la suppression de l'assurance.", 0, $e);
        }
    }
}
