<?php

namespace Wws\Provider\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * This provides controllers under the '/' path
 * We can break out other paths into other providers later
 * 
 * @author Matt Durak <durakmat@msu.edu>
 */
class DefaultControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = new ControllerCollection();

        /**
         * @route '/'
         * @name home
         * 
         * This is the home page, it displays the login/register forms
         */
        $controllers->match('/', function(Application $app) {
            $loginForm = $app['form.factory']->create(new \Wws\Form\LoginType());
            $regForm = $app['form.factory']->create(new \Wws\Form\RegisterType());

            if ('POST' === $app['request']->getMethod()) {
                if($app['request']->request->has('register')) {
                    $regForm->bindRequest($app['request']);
                    //Register
                    if ($regForm->isValid()) {
                        $data = $regForm->getData();
                        // validate and optionally redirect
                        try {
                            $success = $app['wws.auth.user_provider']->RegisterUser($data);
                            if ($success) {
                                $app['session']->setFlash('infobar', 'Successfully registered');
                                $user = $app['wws.auth.user_provider']->Authenticate(
                                    $data['username'],
                                    $data['password']);
                                if ($user !== false) {
                                    return $app->redirect($app['url_generator']->generate('welcome'));
                                }
                            }
                            else {
                                if(!is_null($app['wws.mapper.user']->FindByUsername($data['username']))) {
                                    $app['session']->setFlash('reg', 'Username already exists.  Please try again.');
                                }
                            }
                        } catch (Exception $e) {
                            // password is too long?
                            $app['session']->setFlash('reg', 'FAIL');
                        }
                    }
                }

                //login
                else if($app['request']->request->has('login')) {
                    $loginForm->bindRequest($app['request']);

                    if ($loginForm->isValid()) {
                        $data = $loginForm->getData();
                        // validate and optionally redirect
                        try {
                            $user = $app['wws.auth.user_provider']->Authenticate(
                                $data['username'],
                                $data['password']);
                            if ($user !== false) {
                                return $app->redirect($app['url_generator']->generate('welcome'));
                            } else {
                                // bad email or password
                                if (is_null($app['wws.mapper.user']->FindByUsername($data['username']))) {
                                    $app['session']->setFlash('login', 'The Username entered does not exist');
                                } else if ($app['wws.mapper.user']->FindByUsername($data['username'])->GetPassword() != $data['password']) {
                                    $app['session']->setFlash('login', 'The password entered is incorrect');
                                }
                            }
                        } catch (Exception $e) {
                            // password is too long?
                            $app['session']->setFlash('login', $e);
                        }
                    }
                }
            }

            return $app['twig']->render('homepage-template.html.twig', array(
                'loginform' => $loginForm->createView(),
                'regform' => $regForm->createView()
            ));
        })
        ->middleware($app['wws.auth.must_not_be_logged_in'])
        ->bind('home');
        
        /**
         * @route '/ping'
         * @name ping
         * 
         * This page can be pinged to update the user activity timestamp
         */
        $controllers->match('/ping', function(Application $app, Request $request) {
            if (!is_null($app['wws.user'])) {
                if ($request->get('action') == 'game') {
                    $app['wws.auth.user_provider']->UpdateInGameStatus($app['wws.user'], true);
                } else {
                    $app['wws.auth.user_provider']->UpdateInGameStatus($app['wws.user'], false);
                }
                return new Response(json_encode($request->get('action')), 200);
            }
            return new Response('OK', 200);
        })
        ->bind('ping');
        
        /**
         * @route '/welcome'
         * @name welcome
         * @pre User is logged in
         * 
         * This page is shown after a user logs in
         */
        $controllers->get('/welcome', function(Application $app) {
            
            $games = $app['wws.mapper.game']->FindGamesByUserId($app['wws.user']->GetID(), 1, 'playing');

            $recievedChallenges = $app['wws.mapper.challenge']->FindRecievedChallengesByUserId($app['wws.user']->GetID(), 'pending');
            $sentChallenges = $app['wws.mapper.challenge']->FindSentChallengesByUserId($app['wws.user']->GetID(), 'pending');
            $acceptedChallenges = $app['wws.mapper.challenge']->FindSentChallengesByUserId($app['wws.user']->GetID(), 'accepted');
            //$declinedChallenges = $app['wws.mapper.challenge']->FindSentChallengesByUserId($app['wws.user']->GetID(), 'declined');


            return $app['twig']->render('welcome-template.html.twig', array(
                'games' => $games,
                'recievedChallenges' => $recievedChallenges,
                'sentChallenges' => $sentChallenges,
                'acceptedChallenges' => $acceptedChallenges
            ));
        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('welcome');
		
		 /**
         * @route '/leaderboard'
         * @name leaderboard
         * @pre User is logged in
         * 
         * This page is shown when the user clicks the Leaderboard link
         */
        $controllers->get('/leaderboard', function(Application $app) {
            
            $leaders = $app['wws.mapper.user']->GetLeaderboard();

            return $app['twig']->render('leaderboard-template.html.twig', array(
                'leaders' => $leaders
            ));
        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('leaderboard');
		
		/**
         * @route '/history'
         * @name history
         * @pre User is logged in
         * 
         * This page is shown when the user clicks the History link
         */
        $controllers->get('/history', function(Application $app) {
            
            $games = $app['wws.mapper.game']->FindGamesByUserID($app['wws.user']->GetID(), 0, 'any');

            return $app['twig']->render('history-template.html.twig', array(
                'games' => $games
            ));
        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('history');
        
		/**
         * @route '/history-details/{id}'
         * @name history-details
         * @pre User is logged in
         * 
         * This page is shown when the user clicks the "View Details" link from History page
         */
        $controllers->get('/history-details/{id}', function(Application $app, $id) {
            
            $game = $app['wws.mapper.game']->FindByID($id);
			
			$guesses = $app['wws.mapper.guess']->FindByGame($id);

            return $app['twig']->render('history-details-template.html.twig', array(
                'game' => $game,
				'guesses' => $guesses
            ));
        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('history_details');
        
		/**
         * @route '/view-players'
         * @name view-players
         * @pre User is logged in
         * 
         * This page is shown when the user clicks the Leaderboard link
         */
        $controllers->get('/view_players', function(Application $app) {
            
            $players = $app['wws.mapper.user']->GetAllPlayers();

            return $app['twig']->render('view-players-template.html.twig', array(
                'players' => $players
            ));
        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('view_players');
		
		 /**
         * @route '/edit_profile'
         * @name edit_profile
         * 
         * This page allows the user to edit their profile information, it displays the edit profile forms
         */
        $controllers->match('/edit_profile', function(Application $app) {
            $editForm = $app['form.factory']->create(new \Wws\Form\EditProfileType());
            
            $editForm->setData($app['wws.user']);
            
            if ('POST' === $app['request']->getMethod()) {
                $editForm->bindRequest($app['request']);
                //Register
                if ($editForm->isValid()) {
                    $data = $editForm->getData();
                    // validate and optionally redirect
                    try {
                        $success = $app['wws.auth.user_provider']->UpdateUserProfile($data);
                        if (!$success) {
                            $app['session']->setFlash('edit', 'Error message goes here.');
                        }
                    } catch (Exception $e) {
                        // password is too long?
                        $app['session']->setFlash('edit', 'FAIL');
                    }
                }
            }
            
            return $app['twig']->render('edit-profile-template.html.twig', array(
                'editform' => $editForm->createView()
            ));
        })
        ->bind('edit_profile');
		
		 /**
         * @route '/edit_admin_profile'
         * @name edit_admin_profile
         * 
         * This page allows the admin to edit their profile information, it displays the edit profile forms
         */
        $controllers->match('/edit_admin_profile', function(Application $app) {
            $editForm = $app['form.factory']->create(new \Wws\Form\EditAdminProfileType());
            
            $editForm->setData($app['wws.user']);
            
            if ('POST' === $app['request']->getMethod()) {
                $editForm->bindRequest($app['request']);
                //Register
                if ($editForm->isValid()) {
                    $data = $editForm->getData();
                    // validate and optionally redirect
                    try {
                        $success = $app['wws.auth.user_provider']->UpdateUserProfile($data);
                        if (!$success) {
                            $app['session']->setFlash('edit', 'Error message goes here.');
                        }
                    } catch (Exception $e) {
                        // password is too long?
                        $app['session']->setFlash('edit', 'FAIL');
                    }
                }
            }
            
            return $app['twig']->render('edit-admin-profile-template.html.twig', array(
                'editform' => $editForm->createView()
            ));
        })
        ->bind('edit_admin_profile');

		
        /**
         * @route '/logout'
         * @name logout
         * @pre User is logged in
         * 
         * This logs a user out by clearing the cookie
         */
        $controllers->get('/logout', function(Application $app) {
            $app['wws.auth.user_provider']->Logout();
            return $app->redirect($app['url_generator']->generate('home'));

        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('logout');

        return $controllers;
    }
}