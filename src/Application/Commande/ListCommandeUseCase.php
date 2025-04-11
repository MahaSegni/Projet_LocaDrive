<?php
namespace App\Application\Commande;

use App\Application\Enum\StatutCommande;
use App\Entity\User;
use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;

class ListCommandeUseCase
{
    public function __construct(private CommandeRepository $repository) {}

    public function execute(User $client, ?StatutCommande $statut = null): array
    {
        return $this->repository->findByClient($client, $statut);
    }
}