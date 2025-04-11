<?php

namespace App\Application\Commande;

use App\Entity\Commande;
use App\Application\Enum\StatutCommande;
use Doctrine\ORM\EntityManagerInterface;

class CancelCommandeUseCase
{
    public function __construct(private EntityManagerInterface $em) {}

    public function execute(Commande $commande): void
    {
        if (!in_array($commande->getStatut(), [
            StatutCommande::CART, StatutCommande::EN_ATTENTE
        ])) {
            throw new \LogicException("Cette commande ne peut pas être annulée.");
        }

        $commande->setStatut(StatutCommande::ANNULEE);
        $this->em->flush();
    }
}