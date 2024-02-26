<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CoverageDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Positive]
        #[Type('int')]
        public readonly string $policyId,

        #[Assert\NotBlank]
        #[Assert\Positive]
        #[Type('int')]
        public readonly string $vehicleId,

        #[Assert\NotBlank]
        #[Assert\PositiveOrZero]
        #[Type('int')]
        public readonly string $id,


        #[Assert\NotBlank]
        #[Type('string')]
        public readonly string $type,

        #[Assert\NotBlank]
        #[Assert\Positive]
        #[Type('int')]
        public readonly string $limit,

        #[Assert\NotBlank]
        #[Assert\Positive]
        #[Type('int')]
        public readonly string $deductible,
    ) {
    }
}