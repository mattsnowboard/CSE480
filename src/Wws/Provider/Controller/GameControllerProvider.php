<?php

namespace Wws\Provider\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
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

                if (is_null($game) || $game->getNumPlayers() != 1) {
                    // game not found
                    return $app->abort(404, 'The game you were looking for does not exist');
                }
                $guesses = $app['wws.mapper.guess']->FindByGame($game->getId());
                $game->setGuesses($guesses);
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
        $controllers->post('/guess/single-player/{id}', function(Application $app,
                Request $request, $id) {
            try {
                /**
                 * @var \Wws\Model\Game $game
                 */
                $game = $app['wws.mapper.game']->FindById($id, $app['wws.user']);

                if (is_null($game) || $game->getNumPlayers() != 1) {
                    // game not found
                    return $app->abort(404, 'The game you were looking for does not exist');
                }
                
                $guesses = $app['wws.mapper.guess']->FindByGame($game->getId());
                $game->setGuesses($guesses);
            } catch (\Wws\Exception\NotAuthorizedException $e) {
                throw new HttpException(403, $e->getMessage());
            }
            
            if (!$app['wws.gameplay']->isUserTurn($game, $app['wws.user'])) {
                // it's not their turn yet
                $app['session']->setFlash('gamemsg', 'Wait your turn!');
                return $app->redirect($app['url_generator']->generate('single_player', array(
                    'id' => $game->getId()
                )));
            }
            
            if ($game->isOver()) {
                // it's not their turn yet
                $app['session']->setFlash('gamemsg', 'No more guessing! The game is over');
                return $app->redirect($app['url_generator']->generate('single_player', array(
                    'id' => $game->getId()
                )));
            }
            
            // check post params, we get either 'letter' or 'word' and want to guess it
            $letter = $request->get('letter');
            $word = $request->get('word');
            
            // word can have precendence over letter
            if (!is_null($word) && !empty($word)) {
                // guessed the full word
                $app['session']->setFlash('gamemsg', 'The word "' . $word . '"');

                if (!$app['wws.gameplay']->userCanGuessWord($game, $app['wws.user'])) {
                    // trying to make a 4th letter guess
                    $app['session']->setFlash('gamemsg', 'You cannot guess the letter 4 times');
                } else {
                    $correct = $app['wws.gameplay']->makeWordGuess($game, $app['wws.user'], $word);
                    if ($correct) {
                        $app['session']->setFlash('gamemsg', 'The Word "' . $word . '" is correct! YOU WIN!');
                    } else {
                        $app['session']->setFlash('gamemsg', 'The Word "' . $word . '" is NOT the word! YOU LOSE!');
                    }

                }
            } else if (!is_null($letter) && !empty($letter)) {
                $app['monolog']->addDebug('The user: "' . $app['wws.user']->getId() .'" guessed the letter "' . $letter
                    . '" for the game: "' . $game->getId() . '"');
                if (!$app['wws.gameplay']->userCanGuessLetter($game, $app['wws.user'])) {
                    // trying to make a 4th letter guess
                    $app['session']->setFlash('gamemsg', 'You cannot guess the letter 4 times');
                } else {
                    $correct = $app['wws.gameplay']->makeLetterGuess($game, $app['wws.user'], $letter);
                    if ($correct) {
                        $app['session']->setFlash('gamemsg', 'The letter "' . $letter . '" is in the word!');
                    } else {
                        $app['session']->setFlash('gamemsg', 'The letter "' . $letter . '" is NOT in the word!');
                    }
                }
            } else {
                // no guess made
            }
            
            $app['monolog']->addDebug('After guessing the Game score is: ' . $game->getScore1() . ' to ' . $game->getScore2());
            
            return $app->redirect($app['url_generator']->generate('single_player', array(
                'id' => $game->getId()
            )));
        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('guess_single_player');
        
        /**
         * @route '/exit/single-player/{id}'
         * @name exit_single_player
         * @pre User is logged in
         * 
         * Make a guess
         */
        $controllers->get('/exit/single-player/{id}', function(Application $app,
                Request $request, $id) {
            try {
                /**
                 * @var \Wws\Model\Game $game
                 */
                $game = $app['wws.mapper.game']->FindById($id, $app['wws.user']);

                if (is_null($game) || $game->getNumPlayers() != 1) {
                    // game not found
                    return $app->abort(404, 'The game you were looking for does not exist');
                }
                
                $guesses = $app['wws.mapper.guess']->FindByGame($game->getId());
                $game->setGuesses($guesses);
            } catch (\Wws\Exception\NotAuthorizedException $e) {
                throw new HttpException(403, $e->getMessage());
            }
            
            if ($game->isOver()) {
                $app['session']->setFlash('gamemsg', 'You\'ve already given up on this game');
            } else {
                $app['wws.gameplay']->exitGame($game, $app['wws.user']);
                $app['session']->setFlash('gamemsg', 'Giving up already?');

                $app['monolog']->addDebug('The user quit game "' . $game->getId() . '", final score is: ' . $game->getScore1());
            }
            
            return $app->redirect($app['url_generator']->generate('single_player', array(
                'id' => $game->getId()
            )));
        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('exit_single_player');
        
        return $controllers;
    }
}