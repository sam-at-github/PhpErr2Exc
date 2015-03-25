# PhpErr2Exc
Include file that maps legacy PHP errors to exceptions via PHP's ErrorException class.

PHP has two error flagging mechanisms, the old triggered errors, and newer exceptions. You want to use one or the other for clean and consistent coding. Legacy PHP code uses errors and you cant avoid that. PHP provides ErrorExpection to allow you map errors to exceptions. This PHP file does that but there are some things to note:

 * Only some user handle-able errors are mapped to exceptions. We make a decision about which error logically should map to exceptions.
 * Error logging is handled via usual built-in functions except, by default, rerouted errors are not logged since you have opportunity to log the exception.
 * You still need a global uncaught exception handler and uncaught error handler. The script `set_error_get_last.php` uses `Ec.php` and maps uncaught exceptions to global errors.

## Mapping
Lets define three broad category of general erroneous conditions: ERROR, EXCEPTION, NOTICE.

 * The ERROR class be unconditionally fatal.
 * The EXCEPTION class map to PHP exceptions which are conditionally fatal.
 * The NOTICE class be handled equivalently to a non fatal PHP error. I.e. we get a log of the condition.

Note the following error types are handle-able by the user as of PHP 5.2:

        E_NOTICE, E_USER_NOTICE, E_WARNING, E_USER_WARNING, E_RECOVERABLE_ERROR, E_USER_ERROR, E_USER_DEPRECATED, E_DEPRECATED

The following error types cannot be handled with a user defined function in 5.2:

        E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING,
        and most of E_STRICT raised in the file where set_error_handler() is called.

So a reasonable mapping - and the mapping used, for the handle-able error types would be (the rest are fatal and in ERROR class):

        ERROR: E_USER_ERROR
        EXCEPTION: E_WARNING E_USER_WARNING E_RECOVERABLE_ERROR
        NOTICE: E_NOTICE E_USER_NOTICE E_USER_DEPRECATED E_DEPRECATED <UNKNOWN>

The handle-able set of error types is unlikely to expand beyond E_USER_ERROR. To account for possible unfortunate addition of error types and those parts of E_STRICT that are not 'most of', we could err on side of caution and make all unknown types into exceptions or not and make them Notices. This script treat tehm as exceptions

## Usage
See  [ec_sample.php](docs/ec_sample.php) for an example.
