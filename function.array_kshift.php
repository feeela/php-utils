<?php

/**
 * Shift an element off the beginning of &$arr
 * return that as single-entry array, using the original key
 * for the value.
 * 
 * @param array $arr
 * @return array
 */
function array_kshift( &$arr )
{
    $keys = array_keys( $arr );
    $r = array( $keys[0] => $arr[$keys[0]] );
    unset( $arr[$keys[0]] );
    return $r;
}
