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
            throw new \InvalidArgumentException('Vehicle not found');
        }

        if (empty($brand) || empty($model) || $dailyRate <= 0) {
            throw new \InvalidArgumentException('Invalid vehicle data');
        }

        $vehicle->setBrand($brand);
        $vehicle->setModel($model);
        $vehicle->setDailyRate($dailyRate);

        $this->repository->update($vehicle);
    }
}