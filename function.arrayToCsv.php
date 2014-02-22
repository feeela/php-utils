<?php

/**
 * Return two-dimensional array as CSV string.
 *
 * @param array $data array of associative arrays
 * @return string CSV output or an empty string on error
 */
function arrayToCsv( array $data )
{
    if( empty( $data ) )
    {
        return '';
    }

    /* display field/column names as first row */
    $csvOutput = implode( ',', array_keys( current( $data ) ) ) . "\n";

    /* get CSV log rows */
    foreach( $data as $row )
    {
        /**
         * Wrap strings in quotes and replaces mistyped tab- and newline-characters.
         *
         * @param string $str
         */
        array_walk( $row, function( &$str ) {
            if( !is_scalar( $str ) )
            {
                $str = implode( '|', $str );
            }
            $str = preg_replace(
                    array( "/\t/", "/\r?\n/", '/"/' ), array( "\\t", "\\n", '""' ), $str
            );
            $str = '"' . $str . '"';
        } );

        $csvOutput .= implode( ',', array_values( $row ) ) . "\n";
    }

    return $csvOutput;
}
