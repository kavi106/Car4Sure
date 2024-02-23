<?php

namespace App\Entity;

use App\Repository\CoverageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoverageRepository::class)]
class Coverage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $coverageLimit = null;

    #[ORM\Column]
    private ?int $deductible = null;

    #[ORM\ManyToOne(inversedBy: 'coverages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vehicle $vehicle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCoverageLimit(): ?int
    {
        return $this->coverageLimit;
    }

    public function setCoverageLimit(int $coverageLimit): static
    {
        $this->coverageLimit = $coverageLimit;

        return $this;
    }

    public function getDeductible(): ?int
    {
        return $this->deductible;
    }

    public function setDeductible(int $deductible): static
    {
        $this->deductible = $deductible;

        return $this;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): static
    {
        $this->vehicle = $vehicle;

        return $this;
    }
}
