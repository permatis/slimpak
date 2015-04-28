<?php
require __DIR__.'/../vendor/autoload.php';

use SlimController\Slim;
use SlimFacades\Facade;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

$file = preg_replace('/\\.[^.\\s]{3,4}$/', '', array_slice(scandir(__DIR__.'/../config/'),2));
foreach (glob(__DIR__.'/../config/*') as $key => $val) {
    $config[$file[$key]] = require $val;
}
/**
 * Set app config for applications.
 */
$app = new Slim($config['app']);
$app->add(new \Slim\Middleware\SessionCookie($config['session']));

/**
 * Middleware for auth if user is logged in.
 */
$authenticateUsers = function () use($app){
    return function () use($app) {
        if (!Sentry::check()) {
            $app->flash('error', 'Login required');
            $app->redirect('../login');
        }
    };
};

/**
 * https://github.com/slimphp/Slim-Views
 * In addition to all of this we also have a few helper functions which are included for both view parsers.
 */
$view = $app->view();
$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);

/**
 * Set aliases config and facade applications
 */
Facade::setFacadeApplication($app);
Facade::registerAliases($config['aliases']);

/**
 * Set database config using Eloquent
 */
$capsule = new DB;
$capsule->addConnection($config['database']);
$capsule->setEventDispatcher(new Dispatcher(new Container));
$capsule->setAsGlobal();
$capsule->bootEloquent();

/**
 * Set mailer config using Swift Mailer.
 */
$app->mailer = function () use($config) {
    $transport = Swift_SmtpTransport::newInstance($config['mail']['host'], $config['mail']['port'], $config['mail']['encryption'])
                ->setUsername($config['mail']['username'])
                ->setPassword($config['mail']['password']);

    $mailer = Swift_Mailer::newInstance($transport);
    return $mailer;
};

$mailer = $app->mailer;

require __DIR__.'/../app/router.php';

