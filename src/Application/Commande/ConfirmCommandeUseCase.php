<?php
namespace App\Application\Commande;

use App\Entity\Commande;
use App\Application\Enum\statutCommande;
use Doctrine\ORM\EntityManagerInterface;

class ConfirmCommandeUseCase
{
    public function __construct(private EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function execute(Commande $commande): void
    {
        if ($commande->getStatut() !== StatutCommande::CART) {
            throw new \LogicException("Seules les commandes en 'panier' peuvent être confirmées.");
        }

        $commande->setStatut(StatutCommande::VALIDEE);
        $this->em->flush();
        }
}