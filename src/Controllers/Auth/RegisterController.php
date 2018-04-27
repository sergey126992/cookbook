<?php
namespace Controllers\Auth;

use Psr\Container\ContainerInterface;
use Services\UserService;
use Slim\Http\Request;
use Slim\Http\Response;


class RegisterController
{
    /**
     * @var UserService
     */
    protected $userService;


    /**
     * RegisterController constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->userService = new UserService($container);
    }

    /**
     * Register New Users from POST Requests to /api/users
     * Request Parameters user[username], user[email], user[password]
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     *
     * @return \Slim\Http\Response
     */
    public function register(Request $request, Response $response): Response
    {
        if ( ! $this->validate($userParams = $request->getParam('user')))
            return $response->withJson(['errors' => 'Validation Error'], 422);

        if($user = $this->userService->create($userParams))
            return $response->withJson(['success' => 'username:' . $user->username . ' registration success']);

        return $response->withJson(['errors' => 'Validation Error'], 422);
    }

    /**
     * Simple validation registration values
     *
     * @param array $values
     *
     * @return bool
     */
    public function validate($values): bool
    {
        return UserService::validateRegisterRequest($values);
    }

}