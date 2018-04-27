<?php
/**
 * Created by PhpStorm.
 * User: serg
 * Date: 26.04.2018
 * Time: 16:21
 */

namespace Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer                                  id
 * @property string                                   title
 * @property string                                   description
 * @property string                                   body
 * @property integer                                  user_id
 * @property integer                                  image
 * @property string                                   created_at
 * @property string                                   updated_at
 */
class Cookbook extends Model
{
    /**
     * @var static
     */
    protected $table = 'cookbooks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'title',
        'description',
        'body',
        'user_id',
        'image',
    ];

}