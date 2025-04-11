<?php
namespace App\Application\Commande;

use App\Entity\Commande;
use App\Entity\Reservation;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Application\Enum\StatutCommande;
use Doctrine\ORM\EntityManagerInterface;

class CreateCommandeUseCase
{
    public function __construct(private EntityManagerInterface $em) {}

    public function execute(User $client, string $modePaiement, array $lignesData): Commande
    {
        $commande = new Commande($client, $modePaiement);

        foreach ($lignesData as $ligne) {
            $vehicule = $this->em->getRepository(Vehicle::class)->find($ligne['vehicle_id']);
            if (!$vehicule) {
                throw new \InvalidArgumentException("Véhicule non trouvé : {$ligne['vehicle_id']}");
            }

            $dateDebut = new \DateTimeImmutable($ligne['dateDebut']);
            $dateFin = new \DateTimeImmutable($ligne['dateFin']);

            $commande->ajouterReservation($vehicule, $dateDebut, $dateFin);
        }

        $this->em->persist($commande);
        $this->em->flush();

        return $commande;
    }
}