<?php
require_once 'Ec.php';
register_shutdown_function("handle_fatals");

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

if($argc > 1) {
  throw new Exception("Thrown exception - unhandled"); // Fatal
}
else {
  trigger_error("E_USER_ERROR", E_USER_ERROR); // Fatal
}

/**
 * Optional global uncaught error / exception handler.
 */
function handle_fatals()
{
  global $error_get_last;
  if($error_get_last) {
    $type = isset($error_get_last['was_exception']) && $error_get_last['was_exception'] ? "exception" : "error";
  }
  $msg = "An unhandled $type occured: '{$error_get_last['message']}'";
  if(ini_get('error_log')) {
    $msg .= " See log file [" . ini_get('error_log') . "] for details.";
  }
  $msg .= " Exiting.\n";
  print($msg);
  exit(1);
}
?>
