# PhpErr2Exc
An include file that maps legacy PHP errors to exceptions via PHP's ErrorException class.

# Usage

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

# Why This Exists
PHP has two error flagging mechanisms, the old triggered errors, and newer exceptions. You want to use one or the other for clean and consistent coding. Legacy PHP code uses errors and you cant avoid that. But PHP provides `ErrorExpection` to allow you map errors to exceptions. requiring `Ec.php` does that essentially but handles some other tid bits too.

 * Only some user handle-able errors are mapped to exceptions. We make a decision about which error logically should map to exceptions.
 * Error logging is handled via usual built-in functions except, by default, rerouted errors are not logged since you have opportunity to log the exception.
 * If you want to handle conventional fatal errors that have to be handled with a global shutdown function that inspects `$error_get_last`. `Ec.php` stuffs uncaught exceptions into the global `$error_get_last` so they can be handled by the same callback.

# Error Mapping
We define three broad category of general erroneous conditions: ERROR, EXCEPTION, NOTICE.

 * The ERROR class be unconditionally fatal.
 * The EXCEPTION class map to PHP exceptions which are conditionally fatal.
 * The NOTICE class be handled equivalently to a non fatal PHP error. I.e. we get a log of the condition.

Note the following error types are handle-able by the user as of PHP 5.2:

        E_NOTICE, E_USER_NOTICE, E_WARNING, E_USER_WARNING, E_RECOVERABLE_ERROR, E_USER_ERROR, E_USER_DEPRECATED, E_DEPRECATED

The following error types cannot be handled with a user defined function in 5.2:

        E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING,
        and most of E_STRICT raised in the file where set_error_handler() is called.

So a reasonable mapping, and the mapping used for the handle-able error types would be (the rest are fatal and in ERROR class):

        ERROR: E_USER_ERROR
        EXCEPTION: E_WARNING E_USER_WARNING E_RECOVERABLE_ERROR
        NOTICE: E_NOTICE E_USER_NOTICE E_USER_DEPRECATED E_DEPRECATED <UNKNOWN>

The handle-able set of error types is unlikely to expand beyond E_USER_ERROR. To account for possible unfortunate addition of error types and those parts of E_STRICT that are not 'most of', we could err on side of caution and make all unknown types into exceptions or not and make them Notices. This script errs and treats them as exceptions.
