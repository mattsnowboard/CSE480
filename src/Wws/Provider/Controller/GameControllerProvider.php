<?php

namespace Wws\Provider\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;

/**
 * This provides controllers under the '/game' path
 * These are all related to playing games
 * 
 * @author Matt Durak <durakmat@msu.edu>
 */
class GameControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = new ControllerCollection();

        /**
         * @route '/testchallenges'
         * @name home
         * @todo DELETE this route
         * 
         * This is a temporary way to test the challenge mapper
         */
        $controllers->get('/challenge/{id}', function(Application $app, $id) {
            $test = $app['wws.mapper.challenge']->FindById($id);
            return var_dump($test);
        });
        
        return $controllers;
    }
}