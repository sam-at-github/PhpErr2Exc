<?php
/**
 * Adds an exception handler that stuffs uncaught exceptions into the global $error_get_last.
 * This way, *fatal exceptions* and errors (which are always fatal) can be handled by referring to the global $error_get_last.
 * You need to do that handle the errors that way, so with this approach you can handle the fatal exceptions that way too.
 */
require_once 'Ec.php';


/**
 * Handle all uncaught exceptions. Incorporating them into our global error handler by stuffing into an ~equivalent $error_get_last.
 * Basically the entire point of this is to undo PHP's $error_get_last['message'] setting from $e->__toString to $e->message.
 * @param e Exception.
 */
function ec_exception_handler(Exception $e)
{
  \PhpErr2Exc\Ec::ec_re_error_log(E_ERROR, "Uncaught ".$e->__toString()."\nthrown", $e->getFile(), $e->getLine());
  \PhpErr2Exc\Ec::ec_set_error_get_last(E_ERROR, $e->getMessage(), $e->getFile(), $e->getLine(), $e, true);
}


/**
 * Reroute uncaught errors into the global $error_get_last we are using.
 * Your error handler should just refer to the global $error_get_last for the last error.
 */
function ec_error_shutdown_handler()
{
  global $error_get_last;
  $unhandled_error = error_get_last();
  if($unhandled_error && ($unhandled_error['type'] & EC_FATAL))
  {
    $error_get_last = $unhandled_error;
  }
}

// Should be registerd first.
register_shutdown_function("ec_error_shutdown_handler");
set_exception_handler("ec_exception_handler");
