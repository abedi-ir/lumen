<?php
namespace Jalno\Lumen;

use Laravel\Lumen\Bootstrap\LoadEnvironmentVariables;


(new LoadEnvironmentVariables(dirname(__DIR__)))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

$app = new Jalno\Lumen\Application(dirname(__DIR__), Jalno\Userpanel\Package::class);

$app->withFacades();
$app->withEloquent();

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    Jalno\Userpanel\Exceptions\Handler::class
);


$app->configure('app');
$app->packages->setupRouter();

return $app;
