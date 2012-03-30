<?php

namespace Wws\Provider\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
         * @route '/single-player/{id}'
         * @name single_player
         * @pre User is logged in
         * 
         * This page is shown during a single player game
         */
        $controllers->get('/single-player/{id}', function(Application $app, $id) {
            try {
                $game = $app['wws.mapper.game']->FindById($id, $app['wws.user']);

                if (is_null($game)) {
                    // game not found
                    return $app->abort(404, 'The game you were looking for does not exist');
                }
            } catch (\Wws\Exception\NotAuthorizedException $e) {
                throw new HttpException(403, $e->getMessage());
            }
            
            return $app['twig']->render('single-player-template.html.twig', array(
                'game' => $game
            ));
        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('single_player');
        
        /**
         * @route '/create/single-player'
         * @name create_single_player
         * @pre User is logged in
         * 
         * Creates a single player game
         */
        $controllers->get('/create/single-player', function(Application $app) {
            $game = $app['wws.factory.game']->CreateSinglePlayerGame($app['wws.user']);
            
            /** @todo Check for failure (null game or exception?) **/
            
            // send player to new game
            return $app->redirect($app['url_generator']->generate('single_player', array(
                'id' => $game->getId()
            )));
        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('create_single_player');
        
        /**
         * @route '/guess/single-player/{id}'
         * @name guess_single_player
         * @pre User is logged in
         * 
         * Make a guess
         */
        $controllers->post('/guess/single-player/{id}', function(Application $app, $id) {
            try {
                $game = $app['wws.mapper.game']->FindById($id, $app['wws.user']);

                if (is_null($game)) {
                    // game not found
                    return $app->abort(404, 'The game you were looking for does not exist');
                }
            } catch (\Wws\Exception\NotAuthorizedException $e) {
                throw new HttpException(403, $e->getMessage());
            }
            
            if (!$app['wws.gameplay']->isUserTurn($app['wws.user'])) {
                // it's not their turn yet
                $app['session']->setFlash('gamemsg', 'Wait your turn!');
                return $app->redirect($app['url_generator']->generate('single_player', array(
                    'id' => $game->getId()
                )));
            }
            
            // check post params, we get either 'letter' or 'word' and want to guess it
            
            
            return $app['twig']->render('single-player-template.html.twig', array(
                'game' => $game
            ));
        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('guess_single_player');
        
        return $controllers;
    }
}