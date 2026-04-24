<?php

namespace App\Enums;

enum EventType: string
{
    case Birthday  = 'birthday';
    case Corporate = 'corporate';
    case Meeting   = 'meeting';
    case Other     = 'other';
}
