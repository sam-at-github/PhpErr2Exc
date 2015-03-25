<?php
require_once 'set_error_get_last.php';
ini_set("error_log", "error.log");
ini_set("log_errors", true);
#ini_set('display_errors', E_ALL);
register_shutdown_function("log_fatal");
trigger_error("This is a notice", E_USER_NOTICE);
throw new Exception("Something exceptional just occured");
trigger_error("Exceptional condition just occured", E_USER_WARNING);
trigger_error("User Error just occured", E_USER_ERROR);

function log_fatal()
{
  global $error_get_last;
  print "An error occured: '{$error_get_last['message']}' " .
    "See " . ini_get('error_log') . " for details. Exiting\n";
  print_r($error_get_last);
  exit(1);
}
?>
