<?php

use Controllers\Auth\RegisterController;
use Controllers\Auth\LoginController;
use Controllers\CookbookController;

// Routes

$app->group('/api',
    function (){
        /** @var \Slim\App $this */

        $this->post('/users', RegisterController::class . ':register');
        $this->post('/users/login', LoginController::class . ':login');

        // Cookbook routes
        $this->get( '/cookbook', CookbookController::class . ':index');
        $this->get( '/cookbook/view/{id}', CookbookController::class . ':view');
        $this->post('/cookbook/create', CookbookController::class . ':create');
        $this->post('/cookbook/update/{id}', CookbookController::class . ':update');
        $this->delete('/cookbook/delete/{id}', CookbookController::class . ':delete');
    });
