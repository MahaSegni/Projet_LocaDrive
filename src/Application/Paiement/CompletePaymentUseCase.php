<?php

namespace App\Application\Paiement;

use App\Entity\User;
use App\Repository\CommandeRepository;

class CompletePaymentUseCase
{
    public function __construct(
        private CommandeRepository $commandeRepository
    ) {
        $this->commandeRepository = $commandeRepository;
    }

    public function execute(User $client, int $commandeId): float
    {
        try {
            $commande = $this->commandeRepository->findById($commandeId);

            if (!$commande) {
                throw new \InvalidArgumentException("Commande introuvable.");
            }

            $commande->verifierClient($client);

            $commande->confirmer();

            $totalPrice = $commande->getTotalPrice();

            $this->commandeRepository->save($commande);

            return $totalPrice;

        } catch (\InvalidArgumentException $e) {
            // En cas d'argument invalide (commande introuvable, réservation introuvable, etc.)
            throw $e;
        } catch (\Throwable $e) {
            // En cas d'erreur inattendue
            throw new \RuntimeException("Erreur serveur : " . $e->getMessage());
        }
    }
}
