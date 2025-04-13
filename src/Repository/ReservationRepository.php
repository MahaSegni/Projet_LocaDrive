<?php
namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\Vehicle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private EntityManagerInterface $em)
    {
        parent::__construct($registry, Reservation::class);
        $this->em = $em;
    }

    /**
     * Sauvegarder une réservation
     */
    public function save(Reservation $reservation): void
    {
        $this->em->persist($reservation);
        $this->em->flush();
    }

    /**
     * Supprimer une réservation
     */
    public function delete(Reservation $reservation): void
    {
        $this->em->remove($reservation);
        $this->em->flush();
    }

    /**
     * Récupérer une réservation par son ID
     */
    public function findById(int $id): ?Reservation
    {
        return $this->find($id);
    }

    /**
     * Mettre à jour une réservation
     */
    public function update(Reservation $reservation): void
    {
        // Comme l'entité est déjà gérée par Doctrine, il suffit de faire un flush
        $this->em->flush();
    }
}