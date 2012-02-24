<?php

/**  Bootstraping */
require_once __DIR__.'/../vendor/Silex/silex.phar';

$app = new Silex\Application();

/** Register with autoloader **/
$app['autoloader']->registerNamespace('Wws', __DIR__);

/** Services */
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