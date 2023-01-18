<?php

namespace App\Enums;

/**
 * 页码
 */
enum PaginateEnum: int
{
    case Default = 15;
    case Three = 3;
    case Nine = 9;
    case ThirtyTwo = 32;
    case Mini  = 5;
    case Small  = 10;
    case Middle  = 20;
    case Large = 30;
}
