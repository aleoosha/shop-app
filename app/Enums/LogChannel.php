<?php

namespace App\Enums;

enum LogChannel: string
{
    case STACK = 'stack';
    case SYSTEM = 'system';
    case ACTIONS = 'actions';
    case STORES = 'stores';
    case PAYMENTS = 'payments';
    case ELASTIC = 'elastic';
    case SECURITY = 'security';
}
