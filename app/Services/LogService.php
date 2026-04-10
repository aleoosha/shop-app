<?php

namespace App\Services;

use App\Contracts\Services\LogServiceContract;
use App\Enums\LogChannel;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Log\LogManager;
use Throwable;

class LogService implements LogServiceContract
{
    private array $sensitiveKeys = ['password', 'card_number', 'token', 'cvv'];

    public function __construct(
        private readonly LogManager $logManager,
        private readonly Guard $auth,
        private readonly Request $request
    ) {}

    public function info(string $message, array $context = [], LogChannel $channel = LogChannel::STACK): void
    {
        $this->logManager->channel($channel->value)
            ->info($message, $this->maskSensitiveData($context));
    }

    public function error(string $message, Throwable $exception, array $context = []): void
    {
        $this->logManager->error($message, array_merge($this->maskSensitiveData($context), [
            'exception' => $exception->getMessage(),
            'file' => "{$exception->getFile()}:{$exception->getLine()}",
        ]));
    }

    public function action(string $actionName, array $data = []): void
    {
        $context = [
            'user_id' => $this->auth->id() ?? 'guest',
            'ip' => $this->request->ip(),
            'data' => $data,
        ];

        $this->info("Action: {$actionName}", $context, LogChannel::ACTIONS);
    }

    private function maskSensitiveData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->maskSensitiveData($value);

                continue;
            }

            if (is_scalar($key) && in_array(strtolower((string) $key), $this->sensitiveKeys)) {
                $data[$key] = '********';
            }
        }

        return $data;
    }
    
    public function warning(string $message, array $context = [], LogChannel $channel = LogChannel::STACK): void
    {
        $this->logManager->channel($channel->value)
            ->warning($message, $this->maskSensitiveData($context));
    }
}
