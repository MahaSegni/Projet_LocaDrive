<?php
namespace App\Repository;

use App\Entity\Commande;
use App\Entity\User;
use App\Application\Enum\StatutCommande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,private EntityManagerInterface $em)
    {
        parent::__construct($registry, Commande::class);
        $this->em = $em;
    }
  
    /**
     * Lister les commandes d'un client (optionnellement par statut)
     */
    public function findByClient(User $client, ?StatutCommande $statut = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.reservations', 'r')
            ->addSelect('r')
            ->where('c.client = :client')
            ->setParameter('client', $client);

        if ($statut) {
            $qb->andWhere('c.statut = :statut')
                ->setParameter('statut', $statut);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Trouver une commande spécifique par ID
     */
    public function findById(int $id): ?Commande
    {
        return $this->find($id);
    }

    /**
     * Sauvegarder une commande (création ou update)
     */
    public function save(Commande $commande): void
    {
        $this->em->persist($commande);
        $this->em->flush();
    }

    /**
     * Supprimer une commande
     */
    public function delete(Commande $commande): void
    {
        $this->em->remove($commande);
        $this->em->flush();
    }
    public function findCartByClient(User $client): ?Commande
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.reservations', 'r')
            ->leftJoin('r.vehicule', 'v')
            ->addSelect('r', 'v')
            ->where('c.client = :client')
            ->andWhere('c.statut = :statut')
            ->setParameter('client', $client)
            ->setParameter('statut', StatutCommande::CART)
            ->getQuery()
            ->getOneOrNullResult();
    }
}