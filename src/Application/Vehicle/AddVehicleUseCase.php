<?php

namespace App\Application\Vehicle;

use App\Entity\Vehicle;
use App\Repository\VehicleRepository;

class AddVehicleUseCase
{
    public function __construct(private VehicleRepository $vehicleRepository) {
        $this->vehicleRepository = $vehicleRepository;
    }


    public function execute(string $brand, string $model, float $dailyRate): void
    {
        $vehicle = new Vehicle($brand, $model, $dailyRate);
        $vehicle->valider();

        $this->vehicleRepository->save($vehicle);
    }
}