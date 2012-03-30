<?php

/**  Bootstraping */
require_once __DIR__.'/../vendor/Silex/silex.phar';

$app = new Silex\Application();

/** Class Autoloader 
 * This helps us include classes without "include" or "require"
 * First line gets our own namespace autoloaded
 */
$app['autoloader']->registerNamespaces(array(
    'Wws'     => __DIR__,
    'Symfony' => __DIR__.'/../vendor',
    'Tyaga'   => __DIR__ . '/../vendor'
));

if (!function_exists('intl_get_error_code')) {
    require __DIR__.'/../vendor/Symfony/Component/Locale/Resources/stubs/functions.php';

    $app['autoloader']->registerPrefixFallback(__DIR__.'/../vendor/Symfony/Component/Locale/Resources/stubs');
}

/** Config Files **/
if (!file_exists(__DIR__.'/config/database.yml')) {
    throw new RuntimeException('You must create your own configuration file ("src/config/database.yml"). See "src/config/database.example.yml" for an example config file.');
}

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
    'twig.path' => array(
        __DIR__.'/Wws/Templates',
        __DIR__.'/../vendor/Symfony/Bridge/Twig/Resources/views/Form'
    ),
    'twig.class_path' => __DIR__.'/../vendor/Twig/lib',
    'twig.options' => array('cache' => __DIR__.'/../cache'),
));

/** Forms helpful stuff **/
$app->register(new Silex\Provider\SymfonyBridgesServiceProvider(), array(
    'symfony_bridges.class_path'  => __DIR__.'/../vendor',
));
$app->register(new Silex\Provider\ValidatorServiceProvider(), array(
    'validator.class_path'    => __DIR__.'/../vendor',
));
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.messages' => array()
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());

/** Our Services */

$app->register(new Wws\Provider\Service\FactoryServiceProvider(), array(
));
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
    // now we have {{ user }} in all twig files!
    $app['twig']->addGlobal('user', $app['wws.user']);
});

$app->error(function (\Exception $e, $code) use($app) {
    // show the helpful debugging if in debug mode
    if ($app['debug']) {
        //return;
    }
    
    switch ($code) {
        case 404:
            $message = 'Hmm, looks like something is missing.';
            break;
        default:
            $message = 'Oh no, something is broken!';
    }

    return $app['twig']->render('error-template.html.twig', array(
        'code' => $code,
        'message' => $message,
        'messageDetail' => $e->getMessage()
    ));
});

/** Routing */

$app->mount('/', new Wws\Provider\Controller\DefaultControllerProvider());
$app->mount('/game', new Wws\Provider\Controller\GameControllerProvider());
$app->mount('/test', new Wws\Provider\Controller\TestControllerProvider());

return $app;