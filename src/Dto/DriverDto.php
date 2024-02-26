<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class DriverDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Positive]
        #[Type('int')]
        public readonly string $policyId,

        #[Assert\NotBlank]
        #[Assert\PositiveOrZero]
        #[Type('int')]
        public readonly string $id,

        #[Assert\NotBlank]
        #[Type('string')]
        public readonly string $firstName,

        #[Assert\NotBlank]
        #[Type('string')]
        public readonly string $lastName,

        #[Assert\NotBlank]
        #[Assert\Date]
        #[Type('string')]
        public readonly string $dateOfBirth,

        #[Assert\NotBlank]
        #[Type('string')]
        public readonly string $gender,

        #[Assert\NotBlank]
        #[Type('string')]
        public readonly string $maritalStatus,

        #[Assert\NotBlank]
        #[Assert\Positive]
        #[Type('int')]
        public readonly string $licenseNumber,

        #[Assert\NotBlank] 
        #[Type('string')]
        public readonly string $licenseState,

        #[Assert\NotBlank]
        #[Type('string')]
        public readonly string $licenseStatus,

        #[Assert\NotBlank]
        #[Assert\Date]
        #[Type('string')]
        public readonly string $licenseEffectiveDate,

        #[Assert\NotBlank]
        #[Assert\Date]
        #[Type('string')]
        public readonly string $licenseExpirationDate,

        #[Assert\NotBlank]
        #[Type('string')]
        public readonly string $licenseClass,
    ) {
    }
}