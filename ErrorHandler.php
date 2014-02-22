<?php

/**
 * ErrorHandler
 * 
 * - Supress normal output on error or exception
 * - Translate PHP errors into exceptions
 * - Print formatted exceptions to the screen
 * 
 * Usage:
 * <code>
 * new ErrorHandler( E_ALL );
 * </code>
 *
 * @author Thomas Heuer <projekte@thomas-heuer.eu>
 */
class ErrorHandler
{

    /**
     * @param int $errorReporting Some PHP error reporting constant; the ini setting 'error_reporting' is used as default.
     */
    function __construct( $errorReporting = null )
    {
        ob_start();

        /* setup error and exception handling */
        error_reporting( ( int ) (!is_null( $errorReporting ) ? $errorReporting : ini_get( 'error_reporting' )) );
        set_error_handler( array( $this, 'errorHandler' ) );
        set_exception_handler( array( $this, 'exceptionHandler' ) );
    }

    public function errorHandler( $errno, $errstr, $errfile, $errline )
    {
        if( !(error_reporting() & $errno ) )
        {
            // This error code is not included in error_reporting; do nothing
            return;
        }
        return $this->exceptionHandler( new \ErrorException( $errstr, $errno, 0, $errfile, $errline ) );
    }

    public function exceptionHandler( \Exception $exception )
    {
        // suppress page output, just display exception at this point
        ob_end_clean();

        // output plain UTF-8 test
        header( 'Content-type: text/plain; charset=UTF-8' );

        echo 'An error has occurred. Could not load page.';

        // get some meaningful string from error code
        $errorCode = array(
            1 => 'E_ERROR', 2 => 'E_WARNING', 4 => 'E_PARSE', 8 => 'E_NOTICE', 16 => 'E_CORE_ERROR',
            32 => 'E_CORE_WARNING', 64 => 'E_COMPILE_ERROR', 128 => 'E_COMPILE_WARNING', 256 => 'E_USER_ERROR',
            512 => 'E_USER_WARNING', 1024 => 'E_USER_NOTICE', 2048 => 'E_STRICT', 4096 => 'E_RECOVERABLE_ERROR',
            8192 => 'E_DEPRECATED', 16384 => 'E_USER_DEPRECATED', 30719 => 'E_ALL'
        );

        $errorCodeIdentifier = isset( $errorCode[$exception->getCode()] ) ? $errorCode[$exception->getCode()] : get_class( $exception );

        $outputFilename = str_replace( $_SERVER['DOCUMENT_ROOT'], '', str_replace( '\\', '/', $exception->getFile() ) );

        echo sprintf( "\n\n%s in %s@%d\n\n%s\n\n-----\n%s\n-----\n", $errorCodeIdentifier, $outputFilename, $exception->getLine(), wordwrap( $exception->getMessage(), 150 ), $exception->getTraceAsString() );
        exit;
    }

}
