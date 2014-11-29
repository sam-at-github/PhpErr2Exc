<?php
/* Test same format different handler. */
define("EC_LOG_RETHROWN", true);
require_once '../ec.php';
echo "PHP Warning:  Something in /home/sam/Projects/php_error_exceptions/test/ec_test3.php on line 9\n";
echo "?===?\n";
try
{
  trigger_error("Something", E_USER_WARNING);
}
catch(Exception $e)
{}
