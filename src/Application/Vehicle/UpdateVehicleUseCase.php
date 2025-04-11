<?php

namespace App\Application\Vehicle;

use App\Repository\VehicleRepository;

class UpdateVehicleUseCase
{
    public function __construct(private VehicleRepository $repository)
    {
       $this->repository = $repository;
    }

    public function execute(int $id, string $brand, string $model, float $dailyRate): void
    {
        $vehicle = $this->repository->findById($id);

        if (!$vehicle) {
            throw new \InvalidArgumentException('Véhicule non trouvé.');
        }

        $vehicle->mettreAJour($brand, $model, $dailyRate);
        $this->repository->update($vehicle);
    }
}