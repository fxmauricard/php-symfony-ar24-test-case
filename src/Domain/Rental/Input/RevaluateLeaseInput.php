<?php

namespace App\Domain\Rental\Input;

use Symfony\Component\Validator\Constraints as Assert;

class RevaluateLeaseInput
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public float $indice;
}
