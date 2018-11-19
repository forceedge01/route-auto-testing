<?php

namespace AutoRoute\Validator;

use Behat\Mink\Mink;

/**
 * EndWithHtmlTag class.
 */
class EndWithHtmlTag
{
    public function validate(Mink $mink)
    {
        return true;
    }
}