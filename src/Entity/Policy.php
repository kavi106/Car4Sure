<?php

namespace App\Entity;

use App\Repository\PolicyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PolicyRepository::class)]
class Policy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $policyStatus = null;

    #[ORM\Column(length: 10)]
    private ?string $policyType = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $policyEffectiveDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $policyExpirationDate = null;

    #[ORM\ManyToOne(inversedBy: 'policies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Person $policyHolder = null;

    #[ORM\ManyToMany(targetEntity: Person::class)]
    private Collection $drivers;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(targetEntity: Vehicle::class, mappedBy: 'policy', cascade: ['persist', 'remove'])]
    private Collection $vehicles;

    public function __construct()
    {
        $this->vehicles = new ArrayCollection();
        $this->drivers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPolicyStatus(): ?string
    {
        return $this->policyStatus;
    }

    public function setPolicyStatus(string $policyStatus): static
    {
        $this->policyStatus = $policyStatus;

        return $this;
    }

    public function getPolicyType(): ?string
    {
        return $this->policyType;
    }

    public function setPolicyType(string $policyType): static
    {
        $this->policyType = $policyType;

        return $this;
    }

    public function getPolicyEffectiveDate(): ?\DateTimeInterface
    {
        return $this->policyEffectiveDate;
    }

    public function setPolicyEffectiveDate(\DateTimeInterface $policyEffectiveDate): static
    {
        $this->policyEffectiveDate = $policyEffectiveDate;

        return $this;
    }

    public function getPolicyExpirationDate(): ?\DateTimeInterface
    {
        return $this->policyExpirationDate;
    }

    public function setPolicyExpirationDate(\DateTimeInterface $policyExpirationDate): static
    {
        $this->policyExpirationDate = $policyExpirationDate;

        return $this;
    }

    public function getPolicyHolder(): ?Person
    {
        return $this->policyHolder;
    }

    public function setPolicyHolder(?Person $policyHolder): static
    {
        $this->policyHolder = $policyHolder;

        return $this;
    }

    /**
     * @return Collection<int, Person>
     */
    public function getDrivers(): Collection
    {
        return $this->drivers;
    }

    public function addDriver(Person $driver): static
    {
        if (!$this->drivers->contains($driver)) {
            $this->drivers->add($driver);
        }

        return $this;
    }

    public function removeDriver(Person $driver): static
    {
        $this->drivers->removeElement($driver);

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Vehicle>
     */
    public function getVehicles(): Collection
    {
        return $this->vehicles;
    }

    public function addVehicle(Vehicle $vehicle): static
    {
        if (!$this->vehicles->contains($vehicle)) {
            $this->vehicles->add($vehicle);
            $vehicle->setPolicy($this);
        }

        return $this;
    }

    public function removeVehicle(Vehicle $vehicle): static
    {
        if ($this->vehicles->removeElement($vehicle)) {
            // set the owning side to null (unless already changed)
            if ($vehicle->getPolicy() === $this) {
                $vehicle->setPolicy(null);
            }
        }

        return $this;
    }
}
