<?php
require_once 'ec.php';
eio_dup2(1, 2);
ini_set("error_log", "php://stdout");
try {
  trigger_error("Foos", E_USER_WARNING);
} catch(Exception $e) {
}
Ec::$EC_LOG_RETHROWN = true;
try {
  trigger_error("Foos", E_USER_WARNING);
} catch(Exception $e) {
}
