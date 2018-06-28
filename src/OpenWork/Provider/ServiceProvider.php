<?php

namespace EasyWeChat\OpenWork\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{

    protected $app;

    /**
     * @param Container $app
     */
    public function register(Container $app)
    {
        $this->app = $app;
        isset($app['provider']) || $app['provider'] = function ($app) {
            return new Client($app);
        };
    }


}