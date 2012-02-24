<?php

/**  Bootstraping */
require_once __DIR__.'/../vendor/Silex/silex.phar';

$app = new Silex\Application();



/** Database **/
$app->register(new Silex\Extension\DoctrineExtension(), array(
    'db.options'            => array(
        'driver'    => 'pdo_mysql',
        'host'      => 'localhost',
		'dbname'	=> 'blog',
		'user'		=> 'ichikawa',
		'password'	=> 'hogehoge'
    ),
    'db.dbal.class_path'    => __DIR__.'/../vendor/doctrine-dbal/lib',
    'db.common.class_path'  => __DIR__.'/../vendor/doctrine-common/lib',
));

/** Twig **/
$app->register(new Silex\Extension\TwigExtension(), array(
    'twig.path' => __DIR__.'/Wws/templates',
    'twig.class_path' => __DIR__.'/../vendor/Twig/lib',
    'twig.options' => array('cache' => __DIR__.'/../cache'),
));

/** Register with autoloader **/
$app['autoloader']->registerNamespace('Wws', __DIR__);

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

return $app;