<?php

namespace AutoRoute;

use Behat\Mink\Mink;

/**
 * ValidatorFactory class.
 */
class ValidatorFactory
{
    private static $validators = [];

    public function get($validator, array $args = [])
    {
        if (isset(self::$validators[$validator])) {
            return self::$validators[$validator];
        }

        return self::$validators[$validator] = new $validator($args);
    }
}
