<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class PolicyDto
{
    public function __construct(
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
        #[Type('string')]
        public readonly string $firstName,

        #[Assert\NotBlank]
        #[Type('string')]
        public readonly string $lastName,

        #[Assert\NotBlank] 
        #[Type('string')]
        public readonly string $policyStatus,

        #[Assert\NotBlank]
        #[Type('string')]
        public readonly string $policyType,

        #[Assert\NotBlank]
        #[Assert\Date]
        #[Type('string')]
        public readonly string $policyEffectiveDate,

        #[Assert\NotBlank]
        #[Assert\Date]
        #[Type('string')]
        public readonly string $policyExpirationDate,
    ) {
    }
}