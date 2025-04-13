<?php

namespace App\Controller;

use App\Application\Assurance\AddInsuranceUseCase;
use App\Application\Assurance\RemoveInsuranceUseCase;
use App\Application\Paiement\CompletePaymentUseCase;
use App\Application\Paiement\UpdatePaymentMethodUseCase;
use App\Application\Reservation\AddReservationToCommandeUseCase;
use App\Application\Reservation\RemoveReservationFromCommandeUseCase;
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
    #[Route('/api/commandes/{id}/reservations', name: 'add_reservation', methods: ['POST'])]
    public function addReservation(
        Commande $commande,
        Request $request,
        AddReservationToCommandeUseCase $useCase
    ): JsonResponse {
        $client = $this->getUser();
        $data = json_decode($request->getContent(), true);

        try {
            $reservation = $useCase->execute(
                $client,
                $commande->getId(),
                $data['vehiculeId'] ?? 0,
                $data['dateDebut'] ?? '',
                $data['dateFin'] ?? ''
            );

            return $this->json([
                'message' => 'Véhicule ajouté à la réservation',
                'reservationId' => $reservation->getId(),
                'prixReservation' => $reservation->getPrice(),
                'prixTotalCommande' => $commande->getTotalPrice()
            ], 201);

        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/api/commandes/{commandeId}/reservations/{reservationId}', name: 'remove_reservation', methods: ['DELETE'])]
    public function removeReservation(
        int $commandeId,
        int $reservationId,
        RemoveReservationFromCommandeUseCase $useCase
    ): JsonResponse {
        $client = $this->getUser();

        try {
            $commande = $useCase->execute($client, $commandeId, $reservationId);

            return $this->json([
                'message' => 'Véhicule retiré de la réservation',
                'prixTotalCommande' => $commande->getTotalPrice()
            ]);

        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/api/commandes/{commandeId}/reservations/{reservationId}/assurance', name: 'add_insurance', methods: ['POST'])]
    public function addInsurance(
        int $commandeId,
        int $reservationId,
        AddInsuranceUseCase $useCase
    ): JsonResponse {
        $client = $this->getUser();

        try {
            $prixTotal = $useCase->execute($client, $commandeId, $reservationId);

            return $this->json([
                'message' => 'Assurance ajoutée à la réservation',
                'prixTotalCommande' => $prixTotal
            ]);

        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/api/commandes/{commandeId}/reservations/{reservationId}/assurance', name: 'remove_insurance', methods: ['DELETE'])]
    public function removeInsurance(
        int $commandeId,
        int $reservationId,
        RemoveInsuranceUseCase $useCase
    ): JsonResponse {
        $client = $this->getUser();

        try {
            $prixTotal = $useCase->execute($client, $commandeId, $reservationId);

            return $this->json([
                'message' => 'Assurance retirée de la réservation',
                'prixTotalCommande' => $prixTotal
            ]);

        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/api/commandes/{id}/mode-paiement', name: 'update_payment_method', methods: ['PUT'])]
    public function updatePaymentMethod(
        Commande $commande,
        Request $request,
        UpdatePaymentMethodUseCase $useCase
    ): JsonResponse {
        $client = $this->getUser();
        $data = json_decode($request->getContent(), true);

        try {
            $useCase->execute($client, $commande->getId(), $data['modePaiement'] ?? 'CB');

            return $this->json([
                'message' => 'Mode de paiement mis à jour',
                'prixTotal' => $commande->getTotalPrice()
            ]);

        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/api/commandes/{id}/payer', name: 'complete_payment', methods: ['POST'])]
    public function completePayment(
        Commande $commande,
        CompletePaymentUseCase $useCase
    ): JsonResponse {
        $client = $this->getUser();

        try {
            $totalPrice = $useCase->execute($client, $commande->getId());

            return $this->json([
                'message' => 'Paiement effectué avec succès',
                'montantTotal' => $totalPrice,
                'statut' => StatutCommande::VALIDEE->value
            ]);

        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }
}