<?php

namespace App\Providers;

use App\Models\Assignment;
use App\Models\DutyRoster;
use App\Models\Soldier;
use App\Models\Warehouse;
use App\Observers\AssignmentObserver;
use App\Observers\DutyRosterObserver;
use App\Observers\SoldierObserver;
use App\Observers\WarehouseObserver;
use Elastic\Elasticsearch\Client as ElasticsearchClient;
use Elastic\Elasticsearch\ClientBuilder;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Matchish\ScoutElasticSearch\ElasticSearch\EloquentHitsIteratorAggregate;
use Matchish\ScoutElasticSearch\ElasticSearch\HitsIteratorAggregate;
use Psr\Http\Client\ClientInterface;

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
        Assignment::observe(AssignmentObserver::class);
        DutyRoster::observe(DutyRosterObserver::class);
    }
}
