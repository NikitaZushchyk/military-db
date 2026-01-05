<?php

namespace App\Providers;

use App\Models\Soldier;
use App\Models\Warehouse;
use App\Observers\SoldierObserver;
use App\Observers\WarehouseObserver;
use Elastic\Elasticsearch\ClientBuilder;
use GuzzleHttp\Client;
use Elastic\Elasticsearch\Client as ElasticsearchClient;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Client\ClientInterface;
use Matchish\ScoutElasticSearch\ElasticSearch\HitsIteratorAggregate;
use Matchish\ScoutElasticSearch\ElasticSearch\EloquentHitsIteratorAggregate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ClientInterface::class, Client::class);

        $this->app->bind(ElasticsearchClient::class, function () {
            return ClientBuilder::create()
                ->setHosts([config('scout.elasticsearch.host', 'http://elasticsearch:9200')])
                ->build();
        });
        $this->app->bind(HitsIteratorAggregate::class, function ($app, $parameters) {
            return new EloquentHitsIteratorAggregate(
                $parameters['results'] ?? [],
                $parameters['callback'] ?? null
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Soldier::observe(SoldierObserver::class);
        Warehouse::observe(WarehouseObserver::class);
    }
}
