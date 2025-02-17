<?php

namespace LaravelPropertyBag\tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Hash;
use LaravelPropertyBag\ServiceProvider;
use LaravelPropertyBag\tests\Classes\Post;
use LaravelPropertyBag\tests\Classes\User;
use LaravelPropertyBag\tests\Classes\Admin;
use LaravelPropertyBag\tests\Classes\Group;
use LaravelPropertyBag\tests\Classes\Comment;
use LaravelPropertyBag\tests\Migrations\CreatePostsTable;
use LaravelPropertyBag\tests\Migrations\CreateUsersTable;
use LaravelPropertyBag\tests\Migrations\CreateGroupsTable;
use LaravelPropertyBag\tests\Migrations\CreateCommentsTable;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Testing property bag register.
     *
     * @var Collection
     */
    protected $registered;
    protected $user;
    protected $app;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication(): Application
    {
        $app = require __DIR__ . '/../vendor/laravel/laravel/bootstrap/app.php';

        $app->register(ServiceProvider::class);

        $app->make(Kernel::class)->bootstrap();

        $this->app = $app;

        parent::setUp();

        $this->app['config']->set('database.default', 'sqlite');

        $this->app['config']->set(
            'database.connections.sqlite.database',
            ':memory:'
        );

        $this->migrate();

        $this->user = $this->makeUser();

        return $app;

    }



    /**
     * Run migrations.
     */
    protected function migrate()
    {
        (new CreateUsersTable())->up();

        (new CreateGroupsTable())->up();

        (new CreatePostsTable())->up();

        (new CreateCommentsTable())->up();

        require_once __DIR__ .
            '/../src/Migrations/2016_09_19_000000_create_property_bag_table.php';

        $userSettingsTable = 'CreatePropertyBagTable';

        (new $userSettingsTable())->up();
    }

    /**
     * Make a user.
     *
     * @param string $name
     * @param string $password
     *
     * @return User
     */
    protected function makeUser(
        $name = 'Sam Wilson',
        $email = 'samwilson@example.com'
    ) {
        return User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make('randomstring'),
        ]);
    }

    /**
     * Make an admin user (should fail to get settings).
     *
     * @param string $name
     * @param string $password
     *
     * @return Admin
     */
    protected function makeAdmin(
        $name = 'Sally Makerson',
        $email = 'sallymakerson@example.com'
    ) {
        return Admin::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make('randomstring'),
        ]);
    }

    /**
     * Make a group.
     *
     * @return Group
     */
    protected function makeGroup()
    {
        return Group::create([
            'name'        => 'Laravel User Group',
            'type'        => 'tech',
            'max_members' => 20,
        ]);
    }

    /**
     * Make a group.
     *
     * @return Group
     */
    protected function makePost()
    {
        return Post::create([
            'title'   => 'Free downloads! Click now!',
            'body'    => 'Spammy message in terrible English.',
            'user_id' => 1,
        ]);
    }

    /**
     * Make a group.
     *
     * @return Group
     */
    protected function makeComment()
    {
        return Comment::create([
            'body' => 'Comment body.',
        ]);
    }
}
