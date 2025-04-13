<?php

namespace App\Application\Paiement;

use App\Entity\User;
use App\Repository\CommandeRepository;

class UpdatePaymentMethodUseCase
{
    public function __construct(
        private CommandeRepository $commandeRepository
    ) {
        $this->commandeRepository = $commandeRepository;
    }

    public function execute(User $client, int $commandeId, string $modePaiement): void
    {
        try {
            $commande = $this->commandeRepository->findById($commandeId);

            if (!$commande) {
                throw new \InvalidArgumentException("Commande introuvable.");
            }

            $commande->mettreAJourModePaiement($client, $modePaiement);

            $this->commandeRepository->save($commande);

        } catch (\InvalidArgumentException $e) {
            // En cas d'argument invalide (commande introuvable, mode de paiement non supporté)
            throw $e;

        } catch (\Throwable $e) {
            // En cas d'erreur inattendue
            throw new \RuntimeException("Erreur serveur : " . $e->getMessage());
        }
    }
}
