<?php

namespace App\Application\Assurance;

use App\Entity\User;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManager;

class AddInsuranceUseCase
{
    public function __construct(
        private CommandeRepository $commandeRepository,
        private EntityManager $entityManager,
    ) {
        $this->commandeRepository = $commandeRepository;
        $this->entityManager = $entityManager;
    }

    public function execute(User $client, int $commandeId, int $reservationId): float
    {
        try {
            $commande = $this->commandeRepository->findById($commandeId);

            if (!$commande) {
                throw new \InvalidArgumentException("Commande introuvable.");
            }

            $commande->verifierClient($client);

            $reservation = $commande->recupererReservationOuEchouer($reservationId);

            $reservation->ajouterAssurance();

            $commande->notifierChangementPrixReservation();

            $this->entityManager->flush();

            return $commande->getPrixTotal();

        } catch (\DomainException | \InvalidArgumentException $e) {
            throw $e;

        } catch (\Throwable $e) {
            throw new \RuntimeException("Une erreur est survenue lors de l'ajout de l'assurance.");
        }
    }
}
