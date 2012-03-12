<?php

/**  Bootstraping */
require_once __DIR__.'/../vendor/Silex/silex.phar';

$app = new Silex\Application();

// DEBUGGING
$app['debug'] = true;

/** Class Autoloader **/
$app['autoloader']->registerNamespaces(array(
    'Wws' => __DIR__,
    'Tyaga' => __DIR__ . '/../vendor'
));

/** Config Files **/
$app->register(new Tyaga\Extension\LoadConfigExtension(), array(
    'loadconfig.load' => array(
        'database' => __DIR__ . '/config/database.yml',
    )
));

/** Database **/
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options'            => array(
        'driver'    => 'pdo_mysql',
        'host'      => $app['config']['database']['server'],
		'dbname'	=> $app['config']['database']['database'],
		'user'		=> $app['config']['database']['user'],
		'password'	=> $app['config']['database']['password']
    ),
    'db.dbal.class_path'    => __DIR__.'/../vendor/Doctrine/lib',
    'db.common.class_path'  => __DIR__.'/../vendor/Doctrine/lib/vendor/doctrine-common/lib',
));

/** Twig **/
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/Wws/Templates',
    'twig.class_path' => __DIR__.'/../vendor/Twig/lib',
    'twig.options' => array('cache' => __DIR__.'/../cache'),
));

/** URL Generation helper **/
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

/** Sessions Helper **/
$app->register(new Silex\Provider\SessionServiceProvider());

/** Our Services */
$app['UserMapper'] = $app->share(function($app) {
    return new Wws\Mapper\UserMapper($app['db']);
});
$app['UserProvider'] = $app->share(function($app) {
    return new Wws\Security\UserProvider($app['UserMapper'], $app['session']);
});

/**
 * This is called before normal routing
 * We will use this to check if a user is logged in and store that user if so.
 * That will allow other code to access the User object
 */
$app->before(function() use($app) {
    // check for user cookie
    // get user object
    $app['session']->start();
    $app['User'] = $app['UserProvider']->GetUser();
});

/**
 * This is used for routes that are only for logged in users
 * @var closure
 */
$mustBeLoggedIn = function(Symfony\Component\HttpFoundation\Request $request) use($app) {
    if (is_null($app['User'])) {
        return $app->redirect($app['url_generator']->generate('home'));
    }
};

/** Routing */

$app->get('/', function(Silex\Application $app) {
    return $app['twig']->render('login-template.html.twig');
})
->bind('home');

$app->post('/login-check', function(Silex\Application $app) {
    // check for login
    if ('POST' === $app['request']->getMethod()) {
        // validate and optionally redirect
        try {
            $user = $app['UserProvider']->Authenticate(
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

$app->post('/register-check', function(Silex\Application $app) {
    // check for login
    if ('POST' === $app['request']->getMethod()) {
        // validate and optionally redirect
        try {
            $success = $app['UserProvider']->RegisterUser(
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

$app->get('/logout', function(Silex\Application $app) {
    $app['UserProvider']->Logout();
    return $app->redirect($app['url_generator']->generate('home'));
    
})
->middleware($mustBeLoggedIn)
->bind('logout');

$app->get('/welcome', function(Silex\Application $app) {
    return $app['twig']->render('welcome-template.html.twig');
})
->middleware($mustBeLoggedIn)
->bind('welcome');

$app->get('/test', function(Silex\Application $app) {
    return $app['twig']->render('login-template.html.twig');
});

return $app;