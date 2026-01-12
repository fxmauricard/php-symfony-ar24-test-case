<?php

namespace App\Domain\Rental\Input;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;

class RevaluateLeaseInput
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public float $indice;
}
