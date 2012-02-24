<?php

/**  Bootstraping */
require_once __DIR__.'/../vendor/Silex/silex.phar';

$app = new Silex\Application();

/** Register with autoloader **/
$app['autoloader']->registerNamespaces(array(
    'Wws' => __DIR__,
    'Tyaga' => __DIR__ . '/../vendor'
));

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
    'db.dbal.class_path'    => __DIR__.'/../vendor/doctrine-dbal/lib',
    'db.common.class_path'  => __DIR__.'/../vendor/doctrine-common/lib',
));

/** Twig **/
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/Wws/Templates',
    'twig.class_path' => __DIR__.'/../vendor/Twig/lib',
    'twig.options' => array('cache' => __DIR__.'/../cache'),
));

/** Our Services */
$app['UserProvider'] = $app->share(function() {
    return new Wws\Mapper\UserMapper;
});

$app->before(function() use($app){
    // check for user cookie
    // get user object
    $app['User'] = $app['UserProvider']->FindById(1);
});

/** Routing */

$app->get('/', function() use($app){
    return 'Hello ' . $app['User']->GetId();
});

$app->get('/test', function() use($app){
    return $app['twig']->render('login-template.html.twig');
});

return $app;