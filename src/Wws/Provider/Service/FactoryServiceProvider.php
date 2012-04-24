<?php

namespace Wws\Provider\Service;

use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * This provides all factories
 * 
 * @author Matt Durak <durakmat@msu.edu> 
 */
class FactoryServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        /**
         * The Game Factory
         * @var closure 
         */
        $app['wws.factory.game'] = $app->share(function($app) {
            return new \Wws\Factory\GameFactory($app['wws.mapper.dictionary'], $app['wws.mapper.game'], $app['wws.mapper.challenge']);
        });
        
        /**
         * The Challenge Factory
         * @var closure 
         */
        $app['wws.factory.challenge'] = $app->share(function($app) {
            return new \Wws\Factory\ChallengeFactory($app['wws.mapper.challenge'], $app['wws.mapper.user']);
        });
        
    }
}