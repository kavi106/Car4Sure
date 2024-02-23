<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateOfBirth = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $gender = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $maritalStatus = null;

    #[ORM\Column(nullable: true)]
    private ?int $licenseNumber = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $licenseState = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $licenseStatus = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $licenseEffectiveDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $licenseExpirationDate = null;

    #[ORM\Column(length: 1, nullable: true)]
    private ?string $licenseClass = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Address $address = null;

    #[ORM\OneToMany(targetEntity: Policy::class, mappedBy: 'policyHolder')]
    private Collection $policies;

    public function __construct()
    {
        $this->policies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): static
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getMaritalStatus(): ?string
    {
        return $this->maritalStatus;
    }

    public function setMaritalStatus(string $maritalStatus): static
    {
        $this->maritalStatus = $maritalStatus;

        return $this;
    }

    public function getLicenseNumber(): ?int
    {
        return $this->licenseNumber;
    }

    public function setLicenseNumber(int $licenseNumber): static
    {
        $this->licenseNumber = $licenseNumber;

        return $this;
    }

    public function getLicenseState(): ?string
    {
        return $this->licenseState;
    }

    public function setLicenseState(string $licenseState): static
    {
        $this->licenseState = $licenseState;

        return $this;
    }

    public function getLicenseStatus(): ?string
    {
        return $this->licenseStatus;
    }

    public function setLicenseStatus(string $licenseStatus): static
    {
        $this->licenseStatus = $licenseStatus;

        return $this;
    }

    public function getLicenseEffectiveDate(): ?\DateTimeInterface
    {
        return $this->licenseEffectiveDate;
    }

    public function setLicenseEffectiveDate(\DateTimeInterface|null $licenseEffectiveDate): static
    {
        $this->licenseEffectiveDate = $licenseEffectiveDate;

        return $this;
    }

    public function getLicenseExpirationDate(): ?\DateTimeInterface
    {
        return $this->licenseExpirationDate;
    }

    public function setLicenseExpirationDate(\DateTimeInterface|null $licenseExpirationDate): static
    {
        $this->licenseExpirationDate = $licenseExpirationDate;

        return $this;
    }

    public function getLicenseClass(): ?string
    {
        return $this->licenseClass;
    }

    public function setLicenseClass(string $licenseClass): static
    {
        $this->licenseClass = $licenseClass;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): static
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, Policy>
     */
    public function getPolicies(): Collection
    {
        return $this->policies;
    }

    public function addPolicy(Policy $policy): static
    {
        if (!$this->policies->contains($policy)) {
            $this->policies->add($policy);
            $policy->setPolicyHolder($this);
        }

        return $this;
    }

    public function removePolicy(Policy $policy): static
    {
        if ($this->policies->removeElement($policy)) {
            // set the owning side to null (unless already changed)
            if ($policy->getPolicyHolder() === $this) {
                $policy->setPolicyHolder(null);
            }
        }

        return $this;
    }
}
