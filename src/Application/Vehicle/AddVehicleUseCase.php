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
        // Business rules
        if (empty($brand) || empty($model)) {
            throw new \InvalidArgumentException("Brand and model are required.");
        }

        if ($dailyRate <= 0) {
            throw new \InvalidArgumentException("Daily rate must be greater than 0.");
        }

        $vehicle = new Vehicle($brand, $model, $dailyRate);

        // Persist to database
        $this->vehicleRepository->save($vehicle);
    }
}