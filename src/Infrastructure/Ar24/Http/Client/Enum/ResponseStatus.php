<?php

namespace App\Infrastructure\Ar24\Http\Client\Enum;

enum ResponseStatus: string
{
    case ERROR = 'ERROR';
    case SUCCESS = 'SUCCESS';
    case MAINTENANCE = 'maintenance';
}
