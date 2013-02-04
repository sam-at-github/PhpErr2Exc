php\_error\_exceptions
====================
An include file that maps legacy PHP Errors to Exceptions via PHP's ErrorException class. Provides a simple framework around that.

PHP has two error flagging mechanisms, the old triggered type, called errors herein, and newer Exceptions. You want to use one or the other for clean and consistent coding. Legacy PHP code uses errors and you cant avoid that. PHP provides ErrorExpection to allow you map errors to exceptions. This PHP file does that but there are some things to note:

  1. Only some user handle-able errors are mapped to exceptions.
  2. Uncaught exceptions are mapped back to errors so they can be logged and handled errors since you have to handle those type of errors anyway. 
  3. The global `$error_get_last` holds the last error (or Exception) description for use in handling fatal errors.
  4. Error logging is handled via usual built-in fns except by def rerouted errors are not logged since you have opportunity to log the Exception.
  5. Uncaught errors must be handled by the old style shutdown error handling in client code - noting uncaught exceptions become uncaught errors.
  6. We have to make assertions about the mapping between PHP errors and exceptions and other error conditions. I.e. we map a subset of them to exceptions and leave the rest.

Mapping
-------
Let us define three broad category of erroneous condition: ERROR, EXCEPTION, NOTICE.

 * The ERROR class be unconditionally fatal.
 * The EXCEPTION class map to PHP Exceptions which are conditionally fatal.
 * The NOTICE class be handled equivalently to a non fatal PHP error. I.e. we get a log of the condition in various places.

Note the following error types are handle-able by the user as of PHP 5.2:
  
        E_NOTICE, E_USER_NOTICE, E_WARNING, E_USER_WARNING, E_RECOVERABLE_ERROR, E_USER_ERROR, E_USER_DEPRECATED, E_DEPRECATED

The following error types cannot be handled with a user defined function in 5.2: 
  
        E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING, 
        and most of E_STRICT raised in the file where set_error_handler() is called.
 
So a reasonable mapping for the handle-able error types would be (the rest are fatal and in ERROR class):

        ERROR: E_USER_ERROR
        EXCEPTION: E_WARNING E_USER_WARNING E_RECOVERABLE_ERROR
        NOTICE: E_NOTICE E_USER_NOTICE E_USER_DEPRECATED E_DEPRECATED <UNKNOWN>
   
The handle-able set of error types is unlikely to expand beyond E_USER_ERROR. To account for possible unfortunate addition of error types and those parts of E_STRICT that are not 'most of'; We could err on side of caution and make all unknown types into Exceptions or not and make them Notices.

Usage
-----

        <?php
        require_once("ec.php");
        ini_set("error_log", "test_error_log.txt");
        register_shutdown_function("log_fatal");
        trigger_error("This is a notice", E_USER_NOTICE);
        throw new Exception("Something exceptional just occured");
        trigger_error("Exceptional condition just occured", E_USER_WARNING);
        trigger_error("User Error just occured", E_USER_ERROR);

        function log_fatal()
        {
          global $error_get_last;
          print "In shutdown\n";
          if($error_get_last)
          {
            if($error_get_last['type'] & error_reporting())
            {
              print "An Error occured with message '".$error_get_last['message']."'. Exiting\n";
            }
          }
          print "\n";
          print_r($error_get_last);
        }
        ?>

