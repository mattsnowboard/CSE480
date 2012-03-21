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
        
        return $controllers;
    }
}