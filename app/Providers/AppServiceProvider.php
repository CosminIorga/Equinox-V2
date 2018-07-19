<?php

namespace Equinox\Providers;

use Illuminate\Support\ServiceProvider;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use Thumper\ConnectionRegistry;
use Thumper\RpcClient;
use Thumper\RpcServer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRpcServer()
            ->registerRpcClient();
    }

    /**
     * Register the RPC Server
     * @return $this
     */
    protected function registerRpcServer()
    {
        $this->app->bind(
            RpcServer::class,
            function () {
                return new RpcServer($this->getRpcConnection());
            }
        );

        return $this;
    }

    /**
     * Register the RPC Client
     * @return $this
     */
    protected function registerRpcClient()
    {
        $this->app->bind(
            RpcClient::class,
            function () {
                return new RpcClient($this->getRpcConnection());
            }
        );

        return $this;
    }

    /**
     * Short function used to retrieve the RPC Connection
     * @return \PhpAmqpLib\Connection\AbstractConnection
     */
    protected function getRpcConnection()
    {
        $defaultConnectionName = 'default';

        $connections = [
            $defaultConnectionName => new AMQPLazyConnection(
                env('RABBITMQ_HOST'),
                env('RABBITMQ_PORT'),
                env('RABBITMQ_LOGIN'),
                env('RABBITMQ_PASSWORD'),
                env('RABBITMQ_VHOST')
            ),
        ];
        $registry = new ConnectionRegistry($connections, $defaultConnectionName);

        return $registry->getConnection();
    }
}
