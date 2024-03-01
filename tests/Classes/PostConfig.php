<?php

namespace LaravelPropertyBag\tests\Classes;

use LaravelPropertyBag\Settings\ResourceConfig;

class PostConfig extends ResourceConfig
{
    /**
     * Registered settings for the user. Register settings by setting name. Each
     * setting must have an associative array set as its value that contains an
     * array of 'allowed' values and a single 'default' value.
     *
     * @var array
     */
    protected $registeredSettings = [];

    /**
     * Return a collection of registered settings.
     *
     * @return Collection
     */
    public function registeredSettings()
    {
        return collect([
            'test_settings1' => [
                'allowed' => ['bananas', 'grapes', 8, 'monkey'],
                'default' => 'monkey',
                'title' => 'Test Setting 1',
                'description' => 'This is a test setting.',
            ],

            'test_settings2' => [
                'allowed' => [true, false],
                'default' => true,
                'title' => 'Test Setting 2',
                'description' => 'This is another test setting.'
            ],

            'test_settings3' => [
                'allowed' => [true, false, 'true', 'false', 0, 1, '0', '1'],
                'default' => false,
                'title' => 'Test Setting 3',
                'description' => 'This is yet another test setting.'
            ],
        ]);
    }
}
