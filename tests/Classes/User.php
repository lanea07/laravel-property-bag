<?php

namespace LaravelPropertyBag\tests\Classes;

use LaravelPropertyBag\Settings\HasSettings;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasSettings;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    protected $fillable = ['name', 'email', 'password'];
    /**
     * Settings config class.
     *
     * @var string
     */
    protected $settingsConfig = 'LaravelPropertyBag\tests\Classes\UserConfig';
}
