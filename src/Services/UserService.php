<?php

namespace Services;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\QueryException;
use Models\User;
use Psr\Container\ContainerInterface;

class UserService
{
    /** @var Manager */
    protected $db;

    /** @var \Psr\Container\ContainerInterface*/
    protected $container;

    /**
     * User Service
     *
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('db');
    }

    /**
     * User create service
     *
     * @param array $userParams
     * @return false|User
     */
    public static function create(array $userParams)
    {
        try{
            $user = new User();
            $user->username = $userParams['username'];
            $user->email = $userParams['email'];
            $user->password = User::getPasswordHash($userParams['password']);
            $user->save();

            return $user;
        }
        catch (QueryException $q){
            return false;
        }
    }

    /**
     * @param array $values
     * @return bool
     */
    public static function validateRegisterRequest($values): bool
    {
        if ($values['username'] && $values['email'] && $values['password'])
            return true;

        return false;
    }

    /**
     * @param array $values
     * @return bool
     */
    public static function validateLoginRequest(array $values): bool
    {
        if ($values['username'] && $values['password'])
            return true;

        return false;
    }
}