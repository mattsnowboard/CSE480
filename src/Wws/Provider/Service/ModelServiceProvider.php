<?php

namespace Wws\Provider\Service;

use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * This provides all gameplay logic services
 * 
 * @author Matt Durak <durakmat@msu.edu> 
 */
class ModelServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        /**
         * The Game Factory
         * @var closure 
         */
        $app['wws.gameplay'] = $app->share(function($app) {
            return new \Wws\Model\GamePlay($app['wws.mapper.dictionary'],
                $app['wws.mapper.game'],
                $app['wws.mapper.guess'],
                $app['wws.mapper.user']);
        });
        
    }
}