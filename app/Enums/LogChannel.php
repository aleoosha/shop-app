<?php

namespace App\Enums;

enum LogChannel: string
{
    case STACK = 'stack';
    case ACTIONS = 'actions';
    case ELASTIC = 'elasticsearch';
}
