<?php
// Pipes unhandled exceptions and to error_get_last and set handler to handle_fatal_error.
require_once 'set_error_get_last.php';
register_shutdown_function("handle_fatal_error");

// Setup logging as per usual.
ini_set("error_log", "error.log");
ini_set("log_errors", true);
ini_set('error_reporting', E_ALL);


trigger_error("This is a notice", E_USER_NOTICE); // Logged.
try {
  throw new Exception("Thrown exception"); // No logged because caught and handled.
}
catch(Exception $e) {
  print("CAUGHT EXCEPTION '" . $e->getMessage() . "'\n");
}
try {
  trigger_error("Triggered E_USER_WARNING", E_USER_WARNING); // Piped to ErrorExpection
}
catch(Exception $e) {
  print("CAUGHT EXCEPTION '" . $e->getMessage() . "'\n");
}
throw new Exception("Thrown exception - un handled"); // Fatal
trigger_error("E_USER_ERROR", E_USER_ERROR); // Fatal

function handle_fatal_error()
{
  global $error_get_last;
  print "An error occured: '{$error_get_last['message']}' " .
    "See " . ini_get('error_log') . " for details. Exiting\n";
  exit(1);
}
?>
