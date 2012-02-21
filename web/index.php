<?php

// get the Silex Application
$app = require __DIR__.'/../src/app.php';

// debug mode?
$app['debug'] = true;

// run it
$app->run();