<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class VehicleDto
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
        public readonly string $street,

        #[Assert\NotBlank]
        #[Type('string')]
        public readonly string $city,

        #[Assert\NotBlank]
        #[Type('string')]
        public readonly string $state,

        #[Assert\NotBlank]
        #[Assert\Positive]
        #[Type('int')]
        public readonly string $zip,

        #[Assert\NotBlank]
        #[Type('int')]
        public readonly string $year,

        #[Assert\NotBlank]
        #[Type('string')]
        public readonly string $make,

        #[Assert\NotBlank] 
        #[Type('string')]
        public readonly string $model,

        #[Assert\NotBlank] 
        #[Assert\Positive]
        #[Type('int')]
        public readonly string $vin,

        #[Assert\NotBlank]
        #[Type('string')]
        public readonly string $usage,

        #[Assert\NotBlank]
        #[Type('string')]
        public readonly string $primaryUse,

        #[Assert\NotBlank]
        #[Type('int')]
        public readonly string $annualMileage,

        #[Assert\NotBlank]
        #[Type('string')]
        public readonly string $ownership,
    ) {
    }
}