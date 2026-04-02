<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use Elastic\Elasticsearch\Client;
use App\Infrastructure\Elasticsearch\Indices\ProductIndexConfig;
use Illuminate\Support\Facades\Artisan;

test('it creates elasticsearch index with correct mapping', function () {
    $client = app(Client::class);
    $indexName = app(ProductIndexConfig::class)->getName();

    if ($client->indices()->exists(['index' => $indexName])->asBool()) {
        $client->indices()->delete(['index' => $indexName]);
    }

    Artisan::call('app:elastic-setup');

    expect($client->indices()->exists(['index' => $indexName])->asBool())->toBeTrue();

    $mapping = $client->indices()->getMapping(['index' => $indexName])->asArray();
    
    expect($mapping[$indexName]['mappings']['properties']['created_at']['type'])->toBe('date');
});
