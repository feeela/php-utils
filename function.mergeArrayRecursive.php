<?php

/**
 * Merges multiple associative arrays into one.
 * Integer keys won't get rewritten/sorted.
 * 
 * @param array $array1
 * @param array $arrayN
 * @return array 
 */
function mergeArrayRecursive( $array1, $arrayN )
{
    $args = func_get_args();
    if( empty( $args ) || !is_array( $args[0] ) )
    {
        return array();
    }

    $output = array_shift( $args );

    foreach( $args as $currentArray )
    {
        if( !is_array( $currentArray ) )
        {
            continue;
        }

        foreach( $currentArray as $key => $value )
        {
            if( is_array( $value ) )
            {
                $output[$key] = static::arrayMergeRecursive( ( array ) $output[$key], $value );
            }
            else
            {
                $output[$key] = $value;
            }
        }
    }

    return $output;
}
