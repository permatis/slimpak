<?php namespace Slimpak;

class SentryError {

    protected static $exception = [
        'Cartalyst\Sentry\Users\LoginRequiredException'         => 'Email field is required.',
        'Cartalyst\Sentry\Users\PasswordRequiredException'      => 'Password field is required.',
        'Cartalyst\Sentry\Users\WrongPasswordException'         => 'Wrong password, try again.',
        'Cartalyst\Sentry\Users\UserNotFoundException'          => 'User was not found.',
        'Cartalyst\Sentry\Users\UserNotActivatedException'      => 'User is not activated',
        'Cartalyst\Sentry\Throttling\UserSuspendedException'    => 'User is suspended.',
        'Cartalyst\Sentry\Throttling\UserBannedException'       => 'User is banned.',
        'Cartalyst\Sentry\Users\UserAlreadyActivatedException'  => 'User is already activated.',
        'Cartalyst\Sentry\Groups\GroupNotFoundException'        => 'Group was not found.',
        'Cartalyst\Sentry\Groups\GroupExistsException'          => 'Group already exists.',
        'Cartalyst\Sentry\Groups\NameRequiredException'         => 'Name field is required.',
    ];

    public static function errors($error)
    {
        return static::$exception[$error];
    }
}