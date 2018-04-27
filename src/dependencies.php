<?php
// DIC configuration
/** @var Pimple\Container $container */

$container = $app->getContainer();

// Auth Server Provider
$container['auth'] = function ($c) {
    return new \Services\Auth($c);
};

// File upload directory
$container['upload_directory'] = __DIR__ . "/../uploads/images/";

// Service factory for the ORM
$container['db'] = function ($container) {
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($container['settings']['db']);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};

