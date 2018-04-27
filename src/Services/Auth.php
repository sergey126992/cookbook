<?php

namespace Services;

use Illuminate\Database\Capsule\Manager;
use Models\User;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Auth
{
    /** @var Manager */
    protected $db;

    /**
     * Auth Service
     *
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('db');
    }

    /**
     * Generated auth token (in production app lets use Authentication (JWT))
     *
     * @return string
     */
    public function generateToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * Attempt to find the user based on username and verify password
     *
     * @param $username
     * @param $password
     *
     * @return bool|User
     */
    public function attempt($username, $password)
    {
        if ( ! $user = User::where('username', $username)->first())
            return false;

        if (password_verify($password,$user->password))
            return $user;

        return false;
    }

    /**
     * request User by token
     *
     * @param $request
     * @return bool|User
     */
    public function requestUser(Request $request)
    {
        if ($token = $request->getParam('token'))
        {
            return $user = User::where('token', $token)->first();
        }

        return null;
    }
}