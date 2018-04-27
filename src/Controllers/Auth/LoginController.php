<?php

namespace Controllers\Auth;

use Services\Auth;
use Services\UserService;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class LoginController
{
    /** @var Auth */
    protected $auth;

    /**
     * RegisterController constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->auth = $container->get('auth');
    }

    /**
     * Return token after successful login
     *
     * Request Parameters user[username], user[password]
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     *
     * @return \Slim\Http\Response
     */
    public function login(Request $request, Response $response)
    {
        if ( ! $this->validate($userParams = $request->getParam('user'))) {
            return $response->withJson(['errors' => ['username or password' => ['is invalid']]], 422);
        }

        if ($user = $this->auth->attempt($userParams['username'], $userParams['password'])) {
            $user->setToken($this->auth->generateToken());

            return $response->withJson(['token' => $user->token]);
        };

        return $response->withJson(['errors' => ['username or password' => ['is invalid']]], 422);
    }

    /**
     * Simple validation login values
     *
     * @param array $values
     * @return bool
     */
    public function validate(array $values): bool
    {
        return UserService::validateLoginRequest($values);
    }

}