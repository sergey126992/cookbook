<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer                                  id
 * @property string                                   email
 * @property string                                   username
 * @property string                                   token
 * @property string                                   password
 * @property string                                   created_at
 * @property string                                   updated_at
 */



class User extends Model
{
    /**
     * @var static
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'email',
        'username',
        'password',
    ];

    public function setToken($token)
    {
        $this->token = $token;
        $this->save();
    }

    public static function getPasswordHash($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

}