<?php

namespace App\Controller;

use App\Application\Vehicle\AddVehicleUseCase;
use App\Application\Vehicle\DeleteVehicleUseCase;
use App\Application\Vehicle\GetAllVehiclesUseCase;
use App\Application\Vehicle\GetVehicleByIdUseCase;
use App\Application\Vehicle\UpdateVehicleUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VehicleController extends AbstractController
{
    #[Route('/api/vehicle', name: 'add_vehicle', methods: ['POST'])]
    public function add_vehicle(Request $request, AddVehicleUseCase $useCase): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $useCase->execute(
                $data['brand'] ?? '',
                $data['model'] ?? '',
                (float)($data['dailyRate'] ?? 0)
            );

            return $this->json(['message' => 'Vehicle added successfully'], 201);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Server error'], 500);
        }
    }

    #[Route('/api/vehicle/{id}', name: 'delete_vehicle', methods: ['DELETE'])]
    public function delete_vehicle(int $id, DeleteVehicleUseCase $useCase): JsonResponse
    {
        try {
            $useCase->execute($id);

            return $this->json(['message' => 'Vehicle deleted successfully'], 200);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Server error'], 500);
        }
    }

    #[Route('/api/vehicles', name: 'get_all_vehicles', methods: ['GET'])]
    public function get_all_vehicles(GetAllVehiclesUseCase $useCase): JsonResponse
    {
        try {
            $vehicles = $useCase->execute();

            $result = [];
            foreach ($vehicles as $vehicle) {
                $result[] = [
                    'id' => $vehicle->getId(),
                    'brand' => $vehicle->getBrand(),
                    'model' => $vehicle->getModel(),
                    'dailyRate' => $vehicle->getDailyRate()
                ];
            }

            return $this->json($result, 200);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Server error'], 500);
        }
    }

    #[Route('/api/vehicle/{id}', name: 'update_vehicle', methods: ['PUT'])]
    public function update_vehicle(int $id, Request $request, UpdateVehicleUseCase $useCase): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $useCase->execute(
                $id,
                $data['brand'] ?? '',
                $data['model'] ?? '',
                (float)($data['dailyRate'] ?? 0)
            );

            return $this->json(['message' => 'Vehicle updated successfully'], 200);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Server error'], 500);
        }
    }
    #[Route('/api/vehicle/{id}', name: 'get_vehicle', methods: ['GET'])]
    public function get_vehicle(int $id, GetVehicleByIdUseCase $useCase): JsonResponse
    {
        try {
            $vehicle = $useCase->execute($id);

            $result = [
                'id' => $vehicle->getId(),
                'brand' => $vehicle->getBrand(),
                'model' => $vehicle->getModel(),
                'dailyRate' => $vehicle->getDailyRate()
            ];

            return $this->json($result, 200);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Server error'], 500);
        }
    }
}