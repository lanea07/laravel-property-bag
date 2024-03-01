<?php

namespace LaravelPropertyBag\tests\Classes;

use LaravelPropertyBag\Settings\ResourceConfig;

class CommentConfig extends ResourceConfig
{
    /**
     * Registered settings for the user. Register settings by setting name. Each
     * setting must have an associative array set as its value that contains an
     * array of 'allowed' values and a single 'default' value.
     *
     * 'title' => null,
     * 'description' => null,
     * @var array
     */
    protected $registeredSettings = [
        'invalid' => [
            'allowed' => ':nope:',
            'default' => null,
            'title' => null,
            'description' => null,
        ],
        'any' => [
            'allowed' => ':any:',
            'default' => 'something',
            'title' => null,
            'description' => null,
        ],
        'alpha' => [
            'allowed' => ':alpha:',
            'default' => 'something',
            'title' => null,
            'description' => null,
        ],
        'alphanum' => [
            'allowed' => ':alphanum:',
            'default' => 'something',
            'title' => null,
            'description' => null,
        ],
        'bool' => [
            'allowed' => ':bool:',
            'default' => false,
            'title' => null,
            'description' => null,
        ],
        'integer' => [
            'allowed' => ':int:',
            'default' => 7,
            'title' => null,
            'description' => null,
        ],
        'numeric' => [
            'allowed' => ':num:',
            'default' => 5,
            'title' => null,
            'description' => null,
        ],
        'range' => [
            'allowed' => ':range=1,5:',
            'default' => 1,
            'title' => null,
            'description' => null,
        ],
        'range2' => [
            'allowed' => ':range=-10,5:',
            'default' => 0,
            'title' => null,
            'description' => null,
        ],
        'string' => [
            'allowed' => ':string:',
            'default' => 'test',
            'title' => null,
            'description' => null,
        ],
        'user_defined' => [
            'allowed' => ':example:',
            'default' => true,
            'title' => null,
            'description' => null,
        ],
    ];
}
