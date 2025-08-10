<?php

namespace App\Enums;

enum RouteStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}