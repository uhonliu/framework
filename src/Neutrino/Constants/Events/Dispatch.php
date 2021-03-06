<?php

namespace Neutrino\Constants\Events;

/**
 * Class Dispatch
 *
 * Contains a list of events related to the area 'dispatch'
 *
 *  @package Neutrino\Constants\Events
 */
final class Dispatch
{
    const BEFORE_DISPATCH_LOOP    = 'dispatch:beforeDispatchLoop';
    const BEFORE_DISPATCH         = 'dispatch:beforeDispatch';
    const BEFORE_NOT_FOUND_ACTION = 'dispatch:beforeNotFoundAction';
    const BEFORE_EXECUTE_ROUTE    = 'dispatch:beforeExecuteRoute';
    const AFTER_INITIALIZE        = 'dispatch:afterInitialize';
    const AFTER_EXECUTE_ROUTE     = 'dispatch:afterExecuteRoute';
    const AFTER_DISPATCH          = 'dispatch:afterDispatch';
    const AFTER_DISPATCH_LOOP     = 'dispatch:afterDispatchLoop';
    const BEFORE_EXCEPTION        = 'dispatch:beforeException';
}
