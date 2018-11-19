<?php

namespace AutoRoute\Validator;

use Behat\Mink\Mink;

/**
 * NotHave500Error class.
 */
class NotHave500Error
{
    public function validate(Mink $mink)
    {
        echo 'Validating error code not 500';
        $mink->assertSession()->statusCodeNotEquals(500);
        $mink->assertSession()->pageTextNotContains('Fatal');
    }
}