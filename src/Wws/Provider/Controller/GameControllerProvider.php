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
         * @route '/single-player'
         * @name single-player
         * @pre User is logged in
         * 
         * This page is shown during a single player game
         */
        $controllers->get('/single-player/{id}', function(Application $app, $id) {
            $game = $app['wws.mapper.game']->FindById($id);
            
            return $app['twig']->render('single-player-template.html.twig', array(
                'game' => $game
            ));
        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('single_player');
        
        return $controllers;
    }
}