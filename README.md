# PhpErr2Exc
An include file that maps legacy PHP errors to exceptions via PHP's ErrorException class.

# Usage

    <?php
    require_once 'Ec.php';
    register_shutdown_function("my_error_shutdown_handler");
    set_exception_handler("my_exception_handler");

    try {
      trigger_error("Triggered E_USER_WARNING", E_USER_WARNING); // Piped to ErrorExpection
    }
    catch(Exception $e) {
      print("CAUGHT EXCEPTION '" . $e->getMessage() . "'\n");
    }
    trigger_error("E_USER_WARNING", E_USER_ERROR); // Piped to ErrorExpection and unhandled.

    function my_exception_handler(Exception $e) {
      \PhpErr2Exc\Ec::ec_re_error_log(E_ERROR, "Uncaught ".$e->__toString()."\nthrown", $e->getFile(), $e->getLine());
    }

    function my_error_shutdown_handler() {
      $unhandled_error = error_get_last();
      print("Asda");
      if($unhandled_error && ($unhandled_error['type'] & EC_FATAL)) {
        global $error_get_last;
        print "An error occured: '{$error_get_last['message']}' " .
          "See " . ini_get('error_log') . " for details. Exiting\n";
        exit(1);
      }
    }

Also see [ec_sample.php](docs/ec_sample.php) for an example.


# Why This Exists
PHP has two error flagging mechanisms, the old triggered errors, and newer exceptions. You want to use one or the other for clean and consistent coding. Legacy PHP code uses errors and you cant avoid that. But PHP provides ErrorExpection to allow you map errors to exceptions. This include but there are some things to note:

 * Only some user handle-able errors are mapped to exceptions. We make a decision about which error logically should map to exceptions.
 * Error logging is handled via usual built-in functions except, by default, rerouted errors are not logged since you have opportunity to log the exception.
 * You still need a global uncaught exception handler and uncaught error handler. The sample script `docs/set_error_get_last.php` uses `Ec.php` and maps uncaught exceptions to global errors.

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
