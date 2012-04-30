<?php

namespace Wws\Provider\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
         * @route '/multi-player/{id}'
         * @name multi_player
         * @pre User is logged in
         * 
         * This page is shown during a multi player game
         */
        $controllers->get('/multi-player/{id}', function(Application $app, $id) {
            try {
                $game = $app['wws.mapper.game']->FindById($id, $app['wws.user']);

                $guesses = $app['wws.mapper.guess']->FindByGame($game->getId());
                $game->setGuesses($guesses);
            } catch (\Wws\Exception\NotAuthorizedException $e) {
                throw new HttpException(403, $e->getMessage());
            }
            
            return $app['twig']->render('multi-player-template.html.twig', array(
                'game' => $game
            ));
        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('multi_player');
        
		
		
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
            //return new Response('no redirect');
            return $app->redirect($app['url_generator']->generate('single_player', array(
                'id' => $game->getId()
            )));
        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('create_single_player');
        
		/**
         * @route '/create/single-player'
         * @name create_single_player
         * @pre User is logged in
         * 
         * Creates a multi player game
         */
        $controllers->get('/create/multi-player', function(Application $app) {
            $game = $app['wws.factory.game']->CreateMultiPlayerGame($app['wws.challenge']);
            
            /** @todo Check for failure (null game or exception?) **/
            
            // send player to new game
            return $app->redirect($app['url_generator']->generate('multi_player', array(
                'id' => $game->getId()
            )));
        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('create_multi_player');
		
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
            $isWordGuess = true;
            
            try {
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
                    $isWordGuess = false;
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
            } catch (\Exception $e) {
                $app['monolog']->addDebug('Exception in guess, duplicate?');
                if ($isWordGuess) {
                    $app['session']->setFlash('gamemsg', 'The word "' . $word . '" has already been guessed! Try something new');
                } else {
                    $app['session']->setFlash('gamemsg', 'The letter "' . $letter . '" has already been guessed! Try something new');
                }
            }
            
            $app['monolog']->addDebug('After guessing the Game score is: ' . $game->getScore1() . ' to ' . $game->getScore2());
            
            return $app->redirect($app['url_generator']->generate('single_player', array(
                'id' => $game->getId()
            )));
        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('guess_single_player');
        
		/**
         * @route '/guess/multi-player/{id}'
         * @name guess_single_player
         * @pre User is logged in
         * 
         * Make a guess
         */
        $controllers->post('/guess/multi-player/{id}', function(Application $app,
                Request $request, $id) {
            try {
                /**
                 * @var \Wws\Model\Game $game
                 */
                $game = $app['wws.mapper.game']->FindById($id, $app['wws.user']);
                
                $guesses = $app['wws.mapper.guess']->FindByGame($game->getId());
                $game->setGuesses($guesses);
            } catch (\Wws\Exception\NotAuthorizedException $e) {
                throw new HttpException(403, $e->getMessage());
            }
            
            if (!$app['wws.gameplay']->isUserTurn($game, $app['wws.user'])) {
                // it's not their turn yet
                $app['session']->setFlash('gamemsg', 'Wait your turn!');
                return $app->redirect($app['url_generator']->generate('multi_player', array(
                    'id' => $game->getId()
                )));
            }
            
            if ($game->isOver()) {
                // it's not their turn yet
                $app['session']->setFlash('gamemsg', 'No more guessing! The game is over');
                return $app->redirect($app['url_generator']->generate('multi_player', array(
                    'id' => $game->getId()
                )));
            }
            
            // check post params, we get either 'letter' or 'word' and want to guess it
            $letter = $request->get('letter');
            $word = $request->get('word');
            $isWordGuess = true;
            
            try {
                // word can have precendence over letter
                if (!is_null($word) && !empty($word)) {
                    // guessed the full word
                    $app['session']->setFlash('gamemsg', 'The word "' . $word . '"');

                    if (!$app['wws.gameplay']->userCanGuessWord($game, $app['wws.user'])) {
                        // trying to make a 20th letter guess
                        $app['session']->setFlash('gamemsg', 'You cannot guess anymore letters');
                    } else {
                        $app['session']->setFlash('gamemsg', 'The Word "' . $word . '" is NOT the word!');
                    }
                } else if (!is_null($letter) && !empty($letter)) {
                    $isWordGuess = false;
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
            } catch (\Exception $e) {
                $app['monolog']->addDebug('Exception in guess, duplicate?');
                if ($isWordGuess) {
                    $app['session']->setFlash('gamemsg', 'The word "' . $word . '" has already been guessed! Try something new');
                } else {
                    $app['session']->setFlash('gamemsg', 'The letter "' . $letter . '" has already been guessed! Try something new');
                }
            }
            
            $app['monolog']->addDebug('After guessing the Game score is: ' . $game->getScore1() . ' to ' . $game->getScore2());
            
            //return new Response('no redirect');
            return $app->redirect($app['url_generator']->generate('multi_player', array(
                'id' => $game->getId()
            )));
        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('guess_multi_player');
		
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
		
		/**
         * @route '/exit/single-player/{id}'
         * @name exit_single_player
         * @pre User is logged in
         * 
         * Make a guess
         */
        $controllers->get('/exit/multi-player/{id}', function(Application $app,
                Request $request, $id) {
            try {
                /**
                 * @var \Wws\Model\Game $game
                 */
                $game = $app['wws.mapper.game']->FindById($id, $app['wws.user']);

                if (is_null($game)) {
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
            
            return $app->redirect($app['url_generator']->generate('multi_player', array(
                'id' => $game->getId()
            )));
        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('exit_multi_player');
        
        /**
         * @route '/send-challenge/{recipient}'
         * @name send_challenge
         * @pre User is logged in
         * 
         * Send a challenge to a player
         */
        $controllers->match('/send-challenge/{recipient}', function(Application $app, $recipient) {
            $challenge = $app['wws.factory.challenge']->CreateChallenge($app['wws.user'], $recipient);
            
            /** @todo Check for failure (null game or exception?) **/
            
            // redirect
            return $app->redirect($app['url_generator']->generate('welcome'));
            
            // show a page 
            /*return $app['twig']->render('challenge-sent-template.html.twig', array(
                'challenge' => $challenge
            ));*/
        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('send_challenge');
        
        /**
         * @route '/challenge-list'
         * @name challenge_list
         * @pre User is logged in
         * 
         * Get links to challenge players
         */
        $controllers->match('/challenge-list', function(Application $app) {
            $users = $app['wws.mapper.user']->GetChallengeEligible(
                    \Wws\Model\User::GetActiveTimeConstant(),
                    $app['wws.user']->getId());
            
            // show a page 
            return $app['twig']->render('challenge-list-template.html.twig', array(
                'users' => $users
            ));
        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('challenge_list');
        
        /**
         * @route '/my-challenges'
         * @name my_challenges
         * @pre User is logged in
         * 
         * Returns JSON with all of the player's challenges so we can dynamically update
         * the welcome page (ajax)
         */
        $controllers->get('/my-challenges', function(Application $app) {
            $receivedChallenges = $app['wws.mapper.challenge']->FindRecievedChallengesByUserId($app['wws.user']->GetID(), 'pending');
            //$receivedChallenges = array(); // for testing
            if (empty($receivedChallenges)) {
                return $app->json('No challenges', 404);
            }
            
            // show a page
            $challengeArray = array_map(function(\Wws\Model\Challenge $c) use($app){
                $arr =  $c->toArray();
                // hack to get accept link working
                $arr['acceptLink'] = $app['url_generator']->generate('accept_challenge', array(
                    'id' => $c->getId()
                ));
                $arr['declineLink'] = $app['url_generator']->generate('decline_challenge', array(
                    'id' => $c->getId()
                ));
                return $arr;
            }, $receivedChallenges);
            return $app->json($challengeArray);
        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('my_challenges');
        
        /**
         * @route '/sent-challenges'
         * @name sent_challenges
         * @pre User is logged in
         * 
         * Returns JSON with all of the player's SENT challenges so we can dynamically update
         * the welcome page (ajax)
         */
        $controllers->get('/sent-challenges', function(Application $app) {
            $sentChallenges = $app['wws.mapper.challenge']->FindSentChallengesByUserId($app['wws.user']->GetID(), 'pending');
            $acceptedChallenges = $app['wws.mapper.challenge']->FindSentChallengesByUserId($app['wws.user']->GetID(), 'accepted', true);
            $declinedChallenges = $app['wws.mapper.challenge']->FindSentChallengesByUserId($app['wws.user']->GetID(), 'declined');
            
            $jsonResults = array();
            
            $jsonResults['pending'] = array_map(function(\Wws\Model\Challenge $c) use($app){
                return $c->toArray();
            }, $sentChallenges);
            $jsonResults['declined'] = array_map(function(\Wws\Model\Challenge $c) use($app){
                return $c->toArray();
            }, $declinedChallenges);
            $jsonResults['accepted'] = array_map(function(\Wws\Model\Challenge $c) use($app){
                $arr = $c->toArray();
                $arr['gameLink'] = $app['url_generator']->generate('multi_player', array(
                    'id' => $c->getGameId()
                ));
                return $arr;
            }, $acceptedChallenges);
                        
            return $app->json($jsonResults);
        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('sent_challenges');
        
        /**
         * @route '/accept-challenge/{id}'
         * @name accept_challenge
         * @pre User is logged in
         * 
         * Accept a challenge
         */
        $controllers->match('/accept-challenge/{id}', function(Application $app, $id) {
            $challenge = $app['wws.mapper.challenge']->FindById($id);
            
            if (is_null($challenge)) {
                throw new HttpException(404, 'We couldn\'t find that challenge!');
            }
            
            // make sure this user is recipient
            if ($challenge->GetRecipientId() != $app['wws.user']->getId()) {
                throw new HttpException(403, 'That\'s not your challenge!');
            }
            
            $game = $app['wws.factory.game']->CreateMultiPlayerGame($challenge);
            
            // redirect
            return $app->redirect($app['url_generator']->generate('multi_player', array(
                'id' => $game->getId()
            )));
        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('accept_challenge');

        /**
         * @route '/decline-challenge/{id}'
         * @name decline_challenge
         * @pre User is logged in
         * 
         * Decline a challenge
         */
        $controllers->match('/decline-challenge/{id}', function(Application $app, $id) {
            $challenge = $app['wws.mapper.challenge']->FindById($id);
            
            if (is_null($challenge)) {
                throw new HttpException(404, 'We couldn\'t find that challenge!');
            }
            
            // make sure this user is recipient
            if ($challenge->GetRecipientId() != $app['wws.user']->getId()) {
                throw new HttpException(403, 'That\'s not your challenge!');
            }
            
            $app['wws.mapper.challenge']->declineChallenge($challenge);
            
            // redirect
            return $app->redirect($app['url_generator']->generate('welcome'));
        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('decline_challenge');

        /**
         * @route '/are-we-there-yet'
         * @name check_if_turn
         * 
         * Keep pinging a game to see if it is your turn
         */
        $controllers->match('/are-we-there-yet/{id}', function(Application $app, Request $request, $id) {
            
            try {
                /**
                 * @var \Wws\Model\Game $game
                 */
                $game = $app['wws.mapper.game']->FindById($id, $app['wws.user']);
            } catch (\Wws\Exception\NotAuthorizedException $e) {
                throw new HttpException(403, $e->getMessage());
            }
            
            if ($app['wws.gameplay']->isUserTurn($game, $app['wws.user']) || $game->isOver()) {
                return new Response('YES');
            } else {
                return new Response('NO');
            }
        })
        ->bind('check_if_turn');
        
        return $controllers;
    }
}