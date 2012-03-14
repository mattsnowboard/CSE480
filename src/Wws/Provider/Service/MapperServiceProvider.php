<?php

namespace Wws\Provider\Service;

use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * This provides all data mappers
 * 
 * @author Matt Durak <durakmat@msu.edu> 
 */
class MapperServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        /**
         * The User Mapper
         * @var closure 
         */
        $app['wws.mapper.user'] = $app->share(function($app) {
            return new \Wws\Mapper\UserMapper($app['db']);
        });
    }
}