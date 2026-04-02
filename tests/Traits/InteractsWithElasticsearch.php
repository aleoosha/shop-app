<?php

namespace Tests\Traits;

use Elastic\Elasticsearch\Client;
use App\Infrastructure\Elasticsearch\Indices\ProductIndexConfig;
use Exception;

trait InteractsWithElasticsearch
{
    protected function setUpElasticsearch(): void
    {
        $client = app(Client::class);
        $indexName = app(ProductIndexConfig::class)->getName();

        if ($client->indices()->exists(['index' => $indexName])->asBool()) {
            $client->indices()->delete(['index' => $indexName]);
        }

        $client->indices()->create([
            'index' => $indexName,
            'body'  => app(ProductIndexConfig::class)->getConfig()
        ]);
    }

    protected function refreshIndex(): void
    {
        $client = app(Client::class);
        $indexName = app(ProductIndexConfig::class)->getName();
        
        if ($client->indices()->exists(['index' => $indexName])->asBool()) {
            $client->indices()->refresh(['index' => $indexName]);
        }
    }
}

