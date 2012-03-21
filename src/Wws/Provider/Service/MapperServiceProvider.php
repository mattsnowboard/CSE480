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
        
        /**
         * The Challenge Mapper
         * @var closure 
         */
        $app['wws.mapper.challenge'] = $app->share(function($app) {
            return new \Wws\Mapper\ChallengeMapper($app['db']);
        });
		
		 /**
         * The Game Mapper
         * @var closure 
         */
        $app['wws.mapper.game'] = $app->share(function($app) {
            return new \Wws\Mapper\GameMapper($app['db']);
        });
        
        /**
         * The Guess Mapper
         * @var closure 
         */
        $app['wws.mapper.guess'] = $app->share(function($app) {
            return new \Wws\Mapper\GuessMapper($app['db']);
        });
    }
}