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

    public function valider(): void
    {
        if (empty($this->brand) || empty($this->model)) {
            throw new \InvalidArgumentException("La marque et le modèle sont obligatoires.");
        }

        if ($this->dailyRate <= 0) {
            throw new \InvalidArgumentException("Le tarif journalier doit être supérieur à 0.");
        }
    }
    public function mettreAJour(string $brand, string $model, float $dailyRate): void
    {
        if (empty($brand) || empty($model)) {
            throw new \InvalidArgumentException("La marque et le modèle sont obligatoires.");
        }

        if ($dailyRate <= 0) {
            throw new \InvalidArgumentException("Le tarif journalier doit être supérieur à 0.");
        }

        $this->brand = $brand;
        $this->model = $model;
        $this->dailyRate = $dailyRate;
    }
}
