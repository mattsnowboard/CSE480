<?php

/**  Bootstraping */
require_once __DIR__.'/../vendor/Silex/silex.phar';

$app = new Silex\Application();

/** Class Autoloader 
 * This helps us include classes without "include" or "require"
 * First line gets our own namespace autoloaded
 */
$app['autoloader']->registerNamespaces(array(
    'Wws'   => __DIR__,
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

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());

/** Our Services */

$app->register(new Wws\Provider\Service\MapperServiceProvider(), array(
));
$app->register(new Wws\Provider\Service\AuthServiceProvider(), array(
));

/**
 * This is called before normal routing
 * We will use this to check if a user is logged in and store that user if so.
 * That will allow other code to access the User object
 */
$app->before(function() use($app) {
    $app['wws.auth.app.before']();
});

/** Routing */

$app->mount('/', new Wws\Provider\Controller\DefaultControllerProvider());
$app->mount('/game', new Wws\Provider\Controller\GameControllerProvider());
$app->mount('/test', new Wws\Provider\Controller\TestControllerProvider());

return $app;