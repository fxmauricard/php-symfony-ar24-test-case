<?php

namespace App\Infrastructure\Ar24\Http\User\Enum;

enum UserStatut: string
{
    case INDIVIDUAL = 'particulier';
    case BUSINESS = 'professionnel';
}

