<?php

declare(strict_types=1);

namespace App\Console\Commands\Elastic;

use App\Infrastructure\Elasticsearch\Indices\ProductIndexConfig;
use Elastic\Elasticsearch\Client;
use Illuminate\Console\Command;
use Exception;

class SetupElasticsearchCommand extends Command
{
    protected $signature = 'app:elastic-setup {--force : Удалить существующие индексы перед созданием}';

    protected $description = 'Создает индексы Elasticsearch с правильными маппингами и настройками';

    public function __construct(private Client $client)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $indices = [
            app(ProductIndexConfig::class),
        ];

        foreach ($indices as $config) {
            $name = $config->getName();
            $this->info("Настройка индекса: {$name}");

            try {
                if ($this->option('force') && $this->client->indices()->exists(['index' => $name])->asBool()) {
                    $this->warn("Удаление существующего индекса {$name}...");
                    $this->client->indices()->delete(['index' => $name]);
                }

                if (!$this->client->indices()->exists(['index' => $name])->asBool()) {
                    $this->client->indices()->create([
                        'index' => $name,
                        'body'  => $config->getConfig()
                    ]);
                    $this->info("Индекс {$name} успешно создан с правильным маппингом.");
                } else {
                    $this->comment("Индекс {$name} уже существует. Используйте --force для пересоздания.");
                }
            } catch (Exception $e) {
                $this->error("Ошибка при настройке индекса {$name}: " . $e->getMessage());
                return self::FAILURE;
            }
        }

    $this->info('Все индексы успешно настроены!');
        return self::SUCCESS;
    }
}
