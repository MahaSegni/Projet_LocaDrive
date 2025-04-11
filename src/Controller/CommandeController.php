<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\User;
use App\Application\Enum\StatutCommande;
use App\Application\Commande\CreateCommandeUseCase;
use App\Application\Commande\ConfirmCommandeUseCase;
use App\Application\Commande\CancelCommandeUseCase;
use App\Application\Commande\ListCommandeUseCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    #[Route('/api/commandes', name: 'create_commande', methods: ['POST'])]
    public function create(
        Request $request,
        CreateCommandeUseCase $useCase
    ): JsonResponse {
        /** @var User $client */
        $client = $this->getUser();
        $data = json_decode($request->getContent(), true);

        try {
            $commande = $useCase->execute(
                $client,
                $data['modePaiement'] ?? 'CB',
                $data['reservations'] ?? []
            );

            return $this->json([
                'message' => 'Commande enregistrée',
                'commandeId' => $commande->getId()
            ], 201);

        } catch (\Throwable $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/api/commandes/{id}/confirmer', name: 'confirm_commande', methods: ['PUT'])]
    public function confirm(
        Commande $commande,
        ConfirmCommandeUseCase $useCase
    ): JsonResponse {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            $useCase->execute($commande);
            return $this->json(['message' => 'Commande confirmée']);
        } catch (\Throwable $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/api/commandes/{id}/annuler', name: 'cancel_commande', methods: ['PUT'])]
    public function cancel(
        Commande $commande,
        CancelCommandeUseCase $useCase
    ): JsonResponse {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            $useCase->execute($commande);
            return $this->json(['message' => 'Commande annulée']);
        } catch (\Throwable $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/api/commandes', name: 'list_commandes', methods: ['GET'])]
    public function list(
        Request $request,
        ListCommandeUseCase $useCase
    ): JsonResponse {
        /** @var User $client */
        $client = $this->getUser();
        $statut = $request->query->get('statut');

        $statutEnum = $statut ? StatutCommande::tryFrom($statut) : null;

        $commandes = $useCase->execute($client, $statutEnum);

        $data = array_map(function (Commande $c) {
            return [
                'id' => $c->getId(),
                'statut' => $c->getStatut()->value,
                'dateCreation' => $c->getDateCreation()->format('Y-m-d H:i'),
                'modePaiement' => $c->getModePaiement(),
                'reservations' => array_map(fn($r) => [
                    'vehicule' => $r->getVehicule()->getId(),
                    'dateDebut' => $r->getDateDebut()->format('Y-m-d'),
                    'dateFin' => $r->getDateFin()->format('Y-m-d')
                ], $c->getReservations()->toArray())
            ];
        }, $commandes);

        return $this->json($data);
    }
}