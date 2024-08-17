<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Broadcast::extend('encrypted-redis', function ($app, $config) {
            return new EncryptedRedisBroadcaster(
                $this->app->make('redis'), $config['connection'] ?? null,
                $this->app['config']->get('database.redis.options.prefix', ''),
                $this->app['config']->get('broadcasting.connections.encrypted-redis.master_key')
            );
        });

        Broadcast::routes(['middleware' => 'auth:sanctum']);

        require base_path('routes/channels.php');
    }
}
