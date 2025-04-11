<?php

namespace App\Application\Commande;

use App\Entity\Commande;
use App\Application\Enum\statutCommande;
use Doctrine\ORM\EntityManagerInterface;

class ConfirmCommandeUseCase
{
    public function __construct(private EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function execute(Commande $commande): void
    {
        $commande->confirmer();
        $this->em->flush();
    }
}