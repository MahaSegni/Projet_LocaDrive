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
        $commande->annuler();
        $this->em->flush();
    }
}