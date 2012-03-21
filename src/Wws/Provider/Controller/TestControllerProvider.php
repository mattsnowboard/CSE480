<?php

namespace Wws\Provider\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;

/**
 * This provides controllers under the '/test' path
 * These are for testing things which we wouldn't load in the real application
 * 
 * @author Matt Durak <durakmat@msu.edu>
 */
class TestControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = new ControllerCollection();

        /**
         * @route '/challenge/{id}'
         * 
         * This is a temporary way to test the challenge mapper
         */
        $controllers->get('/challenge/{id}', function(Application $app, $id) {
            $test = $app['wws.mapper.challenge']->FindById($id);
            return var_dump($test);
        });
		
        /**
         * @route '/game/{id}'
         * 
         * This is a temporary way to test the game mapper
         */
        $controllers->get('/game/{id}', function(Application $app, $id) {
            $test = $app['wws.mapper.game']->FindById($id);
            return var_dump($test);
        });
        
        /**
         * @route '/guess/{id}'
         * 
         * This is a temporary way to test the guess mapper
         */
        $controllers->get('/guess/{id}', function(Application $app, $id) {
            $test = $app['wws.mapper.guess']->FindById($id);
            return var_dump($test);
        });
        
        return $controllers;
    }
}