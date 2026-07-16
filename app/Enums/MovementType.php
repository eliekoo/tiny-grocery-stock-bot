<?php

namespace App\Enums;

enum MovementType: string
{
    case ADD = 'ADD';
    case USE = 'USE';
    case ADJUST = 'ADJUST';
}
