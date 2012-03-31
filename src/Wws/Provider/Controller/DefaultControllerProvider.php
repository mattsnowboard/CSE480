<?php

namespace Wws\Provider\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Response;

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
        $controllers->get('/', function(Application $app) {
            $loginForm = $app['form.factory']->create(new \Wws\Form\LoginType());
            $regForm = $app['form.factory']->create(new \Wws\Form\RegisterType());
            
            return $app['twig']->render('homepage-template.html.twig', array(
                'loginform' => $loginForm->createView(),
                'regform' => $regForm->createView()
            ));
        })
        ->bind('home');
        
        /**
         * @route '/ping'
         * @name ping
         * 
         * This page can be pinged to update the user activity timestamp
         */
        $controllers->get('/ping', function(Application $app) {
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
			
            return $app['twig']->render('welcome-template.html.twig', array(
                'games' => $games,
				'recievedChallenges' => $recievedChallenges
            ));
        })
        ->middleware($app['wws.auth.must_be_logged_in'])
        ->bind('welcome');
        
        /**
         * @route '/login-check' POST
         * @name login
         * 
         * This validates the login form and logs the user in with a cookie
         */
        $controllers->post('/login-check', function(Application $app) {
            // check for login
            $loginForm = $app['form.factory']->create(new \Wws\Form\LoginType());
            if ('POST' === $app['request']->getMethod()) {
                $loginForm->bindRequest($app['request']);
                if ($loginForm->isValid()) {
                    $data = $loginForm->getData();
                    // validate and optionally redirect
                    try {
                        $user = $app['wws.auth.user_provider']->Authenticate(
                            $data['username'],
                            $data['password']);
                        if ($user !== false) {
                            // @todo where to redirect on login?
                            return $app->redirect($app['url_generator']->generate('welcome'));
                        } else {
                            // bad email or password
                            // @todo show error message in Flash message
                        }
                    } catch (Exception $e) {
                        // password is too long?
                        // @todo Show error message in Flash message
                    }
                }
            }

            // go back to home page to try again
            return $app->redirect($app['url_generator']->generate('home'));
        })
        ->bind('login');

        /**
         * @route '/register-check' POST
         * @name register
         * 
         * This validates the register form and registers a new user
         */
        $controllers->post('/register-check', function(Application $app) {
            // check for register info
            $regForm = $app['form.factory']->create(new \Wws\Form\RegisterType());
            if ('POST' === $app['request']->getMethod()) {
                $regForm->bindRequest($app['request']);
                if ($regForm->isValid()) {
                    $data = $regForm->getData();
                    // validate and optionally redirect
                    try {
		      //var_dump($data);
                        $success = $app['wws.auth.user_provider']->RegisterUser(
                            $data);
                        if ($success) {
                            // @todo do something
                            return $app->redirect($app['url_generator']->generate('welcome'));
                        } else {
                            // @todo flash error and redirect
                        }
                    } catch (Exception $e) {
                        // password is too long?
                        // @todo Show error message in Flash message
                    }
                }
            }

            // render registration form
	    //var_dump($data);
	    return $app->redirect($app['url_generator']->generate('home'));
        })
        ->bind('register');

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