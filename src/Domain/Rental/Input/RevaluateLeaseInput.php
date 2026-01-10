<?php

namespace App\Domain\Rental\Input;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;

class RevaluateLeaseInput
{
    #[ApiProperty(
        description: 'Revaluation indice.',
        example: '0.03',
    )]
    #[Assert\NotBlank]
    #[Assert\Positive]
    public float $indice;
}
