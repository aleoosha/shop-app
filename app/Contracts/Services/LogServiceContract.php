<?php

namespace App\Contracts\Services;

use App\Enums\LogChannel;
use Throwable;

interface LogServiceContract
{
    public function info(string $message, array $context = [], LogChannel $channel = LogChannel::STACK): void;
    public function error(string $message, Throwable $exception, array $context = []): void;
    public function action(string $actionName, array $data = []): void;
    public function warning(string $message, array $context = [], LogChannel $channel = LogChannel::STACK): void;
}
