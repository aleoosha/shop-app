<?php

namespace Tests\Traits;

use App\Models\Product;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use App\Infrastructure\Elasticsearch\Indices\ProductIndexConfig;

trait InteractsWithElasticsearch
{
protected function setUpElasticsearch(): void
{
    $host = config('elastic.client.hosts.0') ?? 'http://elasticsearch:9200';
    
    $client = ClientBuilder::create()
        ->setHosts([$host])
        ->build();

    $this->app->instance(Client::class, $client);

    $indexConfig = app(ProductIndexConfig::class);
    $indexName = $indexConfig->getName();

    try {
        $client->indices()->delete(['index' => $indexName]);
    } catch (\Exception $e) {}

    $client->indices()->create([
        'index' => $indexName,
        'body'  => $indexConfig->getConfig()
    ]);
}

    protected function refreshIndex(): void
    {
        $indexName = app(ProductIndexConfig::class)->getName();
        
        app(Client::class)->indices()->refresh(['index' => $indexName]);
    }
}

