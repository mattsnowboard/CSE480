<?php

namespace Wws\Provider\Service;

use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * This provides some helpful services for user authentication and authorization
 * 
 * @author Matt Durak <durakmat@msu.edu> 
 */
class AuthServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        /**
        * This is used for routes that are only for logged in users
        * @var closure
        */
        $app['wws.auth.must_be_logged_in'] = $app->protect(function(\Symfony\Component\HttpFoundation\Request $request) use($app) {
            if (is_null($app['wws.user'])) {
                $app['session']->setFlash('infobar', 'You must be logged in to view that page');
                return $app->redirect($app['url_generator']->generate('home'));
            }
        });
		
		/**
        * This is used for routes that are only for logged in users
        * @var closure
        */
        $app['wws.auth.must_be_admin'] = $app->protect(function(\Symfony\Component\HttpFoundation\Request $request) use($app) {
            if ($app['wws.user']->getIsAdmin() != 1) {
                $app['session']->setFlash('infobar', 'You must be an admin to view that page');
                return $app->redirect($app['url_generator']->generate('welcome'));
            }
        });
        
        /**
        * This is the opposite of the above
        * @var closure
        */
        $app['wws.auth.must_not_be_logged_in'] = $app->protect(function(\Symfony\Component\HttpFoundation\Request $request) use($app) {
            if (!is_null($app['wws.user'])) {
                $app['session']->setFlash('infobar', 'You are already logged in!');
                return $app->redirect($app['url_generator']->generate('welcome'));
            }
        });
        
        /**
         * This is called before other controllers, used to store the user if they are logged in
         * @var closure 
         */
        $app['wws.auth.app.before'] = $app->protect(function() use($app) {
            // check for user cookie
            // get user object
            $app['session']->start();
            $app['wws.user'] = $app['wws.auth.user_provider']->GetUser();
            if (!is_null($app['wws.user'])) {
                $app['wws.auth.user_provider']->UpdateActivity($app['wws.user']);
            }
        });
        
        /**
         * This is used to get users and authenticate them or register them
         */
        $app['wws.auth.user_provider'] = $app->share(function($app) {
            return new \Wws\Security\UserProvider($app['wws.mapper.user'], $app['wws.mapper.challenge'], $app['session']);
        });
    }
}