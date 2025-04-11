<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "vehicle")]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string")]
    private string $brand;

    #[ORM\Column(type: "string")]
    private string $model;

    #[ORM\Column(type: "float")]
    private float $dailyRate;

    public function __construct(string $brand, string $model, float $dailyRate)
    {
        $this->brand = $brand;
        $this->model = $model;
        $this->dailyRate = $dailyRate;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getDailyRate(): float
    {
        return $this->dailyRate;
    }
    public function setBrand(string $brand): void
    {
        $this->brand = $brand;
    }

    public function setModel(string $model): void
    {
        $this->model = $model;
    }

    public function setDailyRate(float $dailyRate): void
    {
        $this->dailyRate = $dailyRate;
        }
}
