<?php

namespace App\Application\Vehicle;

use App\Repository\VehicleRepository;

class DeleteVehicleUseCase
{
    public function __construct(private VehicleRepository $repository)
    {
    }

    public function execute(int $id): void
    {
        $vehicle = $this->repository->findById($id);

        if (!$vehicle) {
            throw new \InvalidArgumentException('Vehicle not found');
        }

        $this->repository->delete($vehicle);
    }
}