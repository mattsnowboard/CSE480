<?php

/**  Bootstraping */
require_once __DIR__.'/../vendor/Silex/silex.phar';

$app = new Silex\Application();

/** Routing */

$app->get('/', function() use($app){
    return 'Hello';
});

return $app;