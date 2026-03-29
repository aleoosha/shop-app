<?php

namespace App\Providers;

use App\Infrastructure\Elasticsearch\Indices\ProductIndexConfig;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;

class ElasticsearchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ProductIndexConfig::class, function () {
            return new ProductIndexConfig();
        });

        $this->app->singleton(Client::class, function () {
            $host = config('elastic.client.hosts.0') ?? 'http://elasticsearch:9200';
            
            return ClientBuilder::create()
                ->setHosts([$host])
                ->build();
        });
    }
}
