<?php

namespace Neutrino\View\Engine\Extensions;

/**
 * Class PhpFunctions
 *
 *  @package Neutrino\View\Engine\Extensions
 */
class PhpFunction
{
    /**
     * This method is called on any attempt to compile a function call
     *
     * @param $name
     * @param $arguments
     *
     * @return null|string
     */
    public function compileFunction($name, $arguments)
    {
        if (function_exists($name)) {
            return $name . '(' . $arguments . ')';
        }

        return null;
    }
}
