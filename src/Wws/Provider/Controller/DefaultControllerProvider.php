<?php

namespace Wws\Provider\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;

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
            return $app['twig']->render('login-template.html.twig');
        })
        ->bind('home');
        
        /**
         * @route '/welcome'
         * @name welcome
         * @pre User is logged in
         * 
         * This page is shown after a user logs in
         */
        $controllers->get('/welcome', function(Application $app) {
            return $app['twig']->render('welcome-template.html.twig');
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
            if ('POST' === $app['request']->getMethod()) {
                // validate and optionally redirect
                try {
                    $user = $app['wws.auth.user_provider']->Authenticate(
                        $app['request']->get('email'),
                        $app['request']->get('password'));
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
            // check for login
            if ('POST' === $app['request']->getMethod()) {
                // validate and optionally redirect
                try {
                    $success = $app['wws.auth.user_provider']->RegisterUser(
                        $app['request']->get('email'),
                        $app['request']->get('password'));
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

            // render registration form
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