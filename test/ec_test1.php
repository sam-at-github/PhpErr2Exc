<?php
/** Test how it should all work. */

require_once( "../ec.php" );
ini_set( "error_log", "test_error_log.txt" );
register_shutdown_function( "log_fatal" );
trigger_error( "This is a notice", E_USER_NOTICE );
throw new Exception( "Something exceptional just occured" );
trigger_error( "Exceptional condition just occured", E_USER_WARNING );
trigger_error( "User Error just occured", E_USER_ERROR );

function log_fatal()
{
	global $error_get_last;
	print "In shutdown\n";
	if( $error_get_last )
	{
		if( $error_get_last['type'] & error_reporting() )
		{
			print "An Error occured with message '".$error_get_last['message']."'. Exiting\n";;	
		}
	}
	print "\n";
	print_r( $error_get_last );
}
?>
