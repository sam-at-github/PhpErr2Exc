<?php
/**
 * Adds an exception handler that stuffs uncaught exceptions into the global $error_get_last.
 * This way fatal exceptions and errors can be handled the same way - by refering to the global $error_get_last.
 */
require_once 'ec.php';


/**
 * Handle all uncaught exceptions. Incorporating them into our global error handler by stuffing into an ~equivalent $error_get_last.
 * Basically the entire point of this is to undo PHP's $error_get_last['message'] setting from $e->__toString to $e->message.
 * @param e Exception.
 */
function ec_exception_handler(Exception $e)
{ 
  // This is how PHP logs uncaught exceptions.
  Ec::ec_re_error_log(E_ERROR, "Uncaught ".$e->__toString()."\nthrown", $e->getFile(), $e->getLine());
  // We need to tell shutdown functions an error occured via $error_get_last.
  // But error_get_last() not set if handler is set so.
  Ec::ec_set_error_get_last(E_ERROR, $e->getMessage(), $e->getFile(), $e->getLine(), $e, true);
}


/**
 * Reroute uncaught errors into the global $error_get_last we are using.
 * Your error handler should just refer to the global $error_get_last for the last error.
 */
function ec_error_shutdown_handler()
{
  global $error_get_last;
  $unhandlable = error_get_last();
  if($unhandlable && ($unhandlable['type'] & EC_FATAL))
  {
    $error_get_last = $unhandlable;
  }
}

// Should be registerd first.
register_shutdown_function("ec_error_shutdown_handler");
set_exception_handler("ec_exception_handler");
