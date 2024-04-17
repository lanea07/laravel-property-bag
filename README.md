# Laravel Property Bag

##### Simple settings for Laravel apps.
  - Easily give multiple resources settings
  - Simple to add additional settings as your app grows
  - Set default settings and limit setting values for security
  - Add Title and Description to settings, so users can understand better what it is or what it does

### Contents
  - [Upgrade Information](#upgrade-information)
  - [About](#about)
  - [Install](#install)
  - [Usage](#usage)
  - [Methods](#methods)
  - [Validation Rules](#validation-rules)
  - [Advanced Configuration](#advanced-configuration)
  - [Testing](#testing)
  - [Contributing](#contributing)

## Upgrade Information
### From 1.0.0 to 2.0.0
#### Major update

> [!CAUTION]
> This update is incompatible with previous releases since it changes the structure of the settings

When using `$model->allowedSetting($key)`, it now returns an 'allowed' key with an array of all allowed values for the setting, plus a `title` and `description` keys which can be used to enhance info about the setting. If no key is provided, it returns an array of settings, each one with this new configuration
```php
protected $registeredSettings = [
    'test_settings1' => [
        'allowed' => ['bananas', 'grapes', 8, 'monkey'],
        'default' => 'monkey',
        'title' => 'Test Setting 1',
        'description' => 'This is a test setting.',
    ],
];
```

### Fork from zachleigh/laravel-property-bag 1.4.0 to lanea07/laravel-property-bag 1.0.0
Update dev dependencies to match:
```
php: "^8.1"
laravel/laravel: "^10.3"
orchestra/testbench: "^8.21",
phpunit/phpunit: "^10.1",
```

### About
Laravel Property Bag gives your application resources savable, secure settings by using a single database property bag table. The benefit of using this kind of settings table, as opposed to say a json blob column on the resource table, is that if in the future you decide to change a setting value, a simple database query can easily take care of it.

### Install
##### 1. Install using composer
Since this is a frok from the original project by zachleigh/laravel-property-bag, if you plan to include this this fork in your project you must include the package name using the latest release, and also include this repository into the composer.json file of your app

###### composer.json
```php
...

"repositories": [
    ...
    {
        "type": "vcs",
        "url": "https://github.com/lanea07/laravel-property-bag",
    },
    ...
],

...

"require": {
    ...
    "lanea07/laravel-property-bag": "^2.0",
    ...
},

...
```

#### Laravel Installation

##### a. Register the service provider
In Laravel's `config/app.php` file, add the service provider to the array with the 'providers' key.
```
LaravelPropertyBag\ServiceProvider::class
```

##### b. Publish the migration
```
php artisan vendor:publish --provider="LaravelPropertyBag\ServiceProvider"
```

##### 2. Run the migration
```
php artisan migrate
```

##### 3. Create a new settings config file for your resource.
```
php artisan pbag:make {resource}
```
{resource} should be the name of the model you wish to add settings to. For example:
```
php artisan pbag:make User
```
This will create a Settings directory containing a UserSettings class where you can configure your settings for the User class.

### Usage
##### 1. Use the trait in the model.
```php
...
use LaravelPropertyBag\Settings\HasSettings;

class User extends Model
{
    use HasSettings;

    ...
}
```

##### 2. Register your settings plus their allowed values and defaults
After publishing the UserSettings file register settings in the UserSettings class.
```php
protected $registeredSettings = [
    'test_settings1' => [
        'allowed' => ['bananas', 'grapes', 8, 'monkey'],
        'default' => 'monkey',
        'title' => 'Test Setting 1',
        'description' => 'This is a test setting.',
    ],
];
```
Each setting must contain an array of allowed values and a default value. It is also possible to use [validation rules](#validation-rules) instead of hardcoding allowed values.
The `title` and `description` keys are used to offer a better understanding of the setting. You can use this, for example, in a frontend where users can change its own settings...

##### 3. Set the setting from the user model
```php
$user->settings(['example_setting' => false]);
// or
$user->settings()->set(['example_setting' => false]);
// or
$user->setSettings(['example_setting' => false]);
```

Set multiple values at a time
```php
$user->settings([
    'example_setting' => false,
    'another_setting' => 'grey'
]);
```

##### 4. Get the set value from the user model
```php
$value = $user->settings('example_setting');
// or
$value = $user->settings()->get('example_setting');
```
If the value has not been set, the registered default value will be returned. **Note that default values are not stored in the database in order to limit database size. This rule also applies to title and description keys.**

### Methods

##### get($key)
Get value for given key.
```php
$value = $model->settings()->get($key);
```

##### set($array)
Set array keys to associated values. Values may be of any type. Returns Settings.
**When a default value is passed to set(), it will not be stored in the database.** Don't be alarmed if your default values aren't showing up in the table.
If a value is not registered in the allowed values array, a `LaravelPropertyBag\Exceptions\InvalidSettingsValue` exception will be thrown. You can use the `$e->getFailedKey()` method to retrieve the failed setting name.
```php
$model->settings()->set([
  'key1' => 'value1',
  'key2' => 'value2'
]);

// or

$model->setSettings([
  'key1' => 'value1',
  'key2' => 'value2'
]);
```

##### getDefault($key)
Get default value for given key.
```php
$default = $model->settings()->getDefault($key);

// or

$default = $model->defaultSetting($key);
```

##### allDefaults()
Get all the default values for registered settings. Returns collection.
```php
$defaults = $model->settings()->allDefaults();

// or

$defaults = $model->defaultSetting();
```

##### getAllowed($key)
Get allowed values for given key. Returns collection.
```php
$allowed = $model->settings()->getAllowed($key);

// or

$allowed = $model->allowedSetting($key);
```

##### allAllowed()
Get all allowed values for registered settings. Returns collection.
```php
$allowed = $model->settings()->allAllowed();

// or

$allowed = $model->allowedSetting();
```

##### isDefault($key, $value)
Return true if given value is the default value for given key.
```php
$boolean = $model->settings()->isDefault($key, $value);
```

##### isValid($key, $value)
Return true if given value is allowed for given key.
```php
$boolean = $model->settings()->isValid($key, $value);
```

##### all()
Return all setting value's for model. Returns collection.
```php
$allSettings = $model->settings()->all();

// or

$allSettings = $model->allSettings();
```

##### keyIs($key, $value)
Return true if setting for given key equals given value.
```
$boolean = $model->settings()->keyIs($key, $value);
```

##### reset($key)
Reset key to default value.
```
$default = $model->settings()->reset($key);
```

##### withSetting($key, $value = null)
Get an array with all stored rows with a given setting and/or value.
```
$collection = $model::withSetting($key);
// or
$collection = $model::withSetting($key, $value);
```

### Validation Rules
Rather than hardcoding values in an array, it is also possible to define rules that determine whether a setting value is valid. Rules are always strings and must contain a colon at both the beginning and ending of the string.
```php
'integer' => [
    'allowed' => ':int:',
    'default' => 7
]
```
In this case, the setting value saved for the 'integer' key must be an integer.

Some rules require parameters. Parameters can be passed in the rule definition by using an equal sign and a comma separated list.
```php
'range' => [
    'allowed' => ':range=1,5:',
    'default' => 1
]
```

#### Available Rules
##### ':any:'
Any value will be accepted.

##### ':alpha:'
Alphabetic values will be accepted.

##### ':alphanum:'
Alphanumeric values will be accepted.

##### ':bool:'
Boolean values will be accepted.

##### ':int:'
Integer values will be accepted.

##### ':num:'
Numeric values will be accepted.

##### ':range=low,high:'
Numeric values falling between or inluding the given low and high parameters will be accpeted. Example:
```php
'range' => [
    'allowed' => ':range=1,10:',
    'default' => 5
]
```
The numbers 1 to 10 will be allowed.

##### ':string:'
Strings will be accepted.

#### User Defined Rules
To make user defined rules, first publish the Rules file to Settings/Resources/Rules.php:
```
php artisan pbag:rules
```
Rule validation methods should be named by prepending 'rule' to the rule name. For example, if our rule is 'example', we would define it in the settings config file like this:
```php
'setting_name' => [
    'allowed' => ':example:',
    'default' => 'default'
]
```
And then our method would be called 'ruleExample':
```php
public static function ruleExample($value)
{
    // do stuff
    //
    // return boolean;
}
```
All rule methods should be static and thus should not care about object or application state. If your rule requires parameters, accept them as arguments to the method.
```php
'setting_name' => [
    'allowed' => ':example=arg1,arg2:',
    'default' => 'default'
]
```

```php
public static function ruleExample($value, $arg1, $arg2)
{
    // do stuff
    //
    // return boolean;
}
```

Another option would be to validate input with Laravel's built in validation, which is much more complete than what this package offers, and then set all your setting allowed values to ':any:'.

### Advanced Configuration
Laravel Property Bag gives you several ways to configure the package to fit your needs and wants.

###### I don't want to register settings as an array
Especially if you have dozens of settings, dealing with an array can be annoying. In the model settings config file, add the registeredSettings method.
```php
/**
 * Return a collection of registered settings.
 *
 * @return Collection
 */
public function registeredSettings()
{
    // Your code

    return $collection;
}
```
In this method, do whatever you want and return a collection of items that has the same structure as the registeredSettings array.
```php
'example_setting' => [
    'allowed' => [true, false],
    'default' => true
    'title' => 'A Title',
    'description' => 'What this setting is or does'
]
```

###### I want to use dynamic allowed and default values.
No problem. Like in the above section, create your own registeredSettings method in the settings config file and return a collection of registered settings.
```php
/**
 * Return a collection of registered settings.
 *
 * @return Collection
 */
public function registeredSettings()
{
    $allGroups = Auth::user()->allGroupNames();

    return collect([
        'default_group' => [
            'allowed' => $allGroups,
            'default' => $allGroups[0]
        ]
    ]);
}
```
The allGroupNames function simply returns an array of group names:
```php
/**
 * Get array of all group names.
 *
 * @return array
 */
public function allgroupNames()
{
    return $this->groups->pluck('name')->all();
}
```

You can also access the model the settings are attached to with the `getResource()` method.
```php
/**
 * Return a collection of registered settings.
 *
 * @return Collection
 */
public function registeredSettings()
{
    $allGroups = getResource()->allGroupNames();

    return collect([
        'default_group' => [
            'allowed' => $allGroups,
            'default' => $allGroups[0]
        ]
    ]);
}
```

## Testing
This upgrade uses laravel's latest testing package :link: [Orchestral Testbench](https://packages.tools/testbench), so after running `composer install` it is recomended to follow instructions about how to setup this package before running tests.

### Contributing
Contributions are more than welcome. Fork, improve and make a pull request. For bugs, ideas for improvement or other, please create an [issue](https://github.com/lanea07/laravel-property-bag/issues).
