<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UserDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Username cannot be blank !')]
        #[Type('string')]
        public readonly string $username,

        #[Assert\NotBlank(message: 'Password cannot be blank !')]
        #[Type('string')]
        public readonly string $password,
    ) {
    }
}
