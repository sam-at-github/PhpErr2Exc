<?php
/* Test mapping from warning to exception. */
require_once '../ec.php';
try
{
  trigger_error( "This is a warning.", E_USER_WARNING );
}
catch( Exception $e )
{
  assert($e->getMessage() === 'This is a warning.');
  assert($e->getCode() === 512);
  assert($e->getLine() === 6);
}
