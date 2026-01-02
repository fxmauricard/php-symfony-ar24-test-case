<?php

namespace App\Infrastructure\Http\User\Enum;

enum Ar24UserStatut: string
{
    case INDIVIDUAL = 'particulier';
    case BUSINESS = 'professionnel';
}

