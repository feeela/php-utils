<?php

/**
 * Return a reference to an array value specified by its key path.
 * 
 * Example:
 * <code>
 * $arr = array(
 *     'a' => array(
 *         'b' => 'c'
 *     )
 * );
 * 
 * // Return a reference to the string 'c'
 * $refC = getArrayValueReference( $arr, array( 'a', 'b' ) );
 * </code>
 * 
 * @param array &$array
 * @param array $arrayKeys
 * @param boolean $create Set to TRUE to create a nested array from the given key path
 * @return reference
 */
function &getArrayValueReference( &$array, $arrayKeys, $create = false )
{
    /* get array entry from first key in list */
    $currentKey = array_shift( $arrayKeys );
    if( !isset( $array[$currentKey] ) )
    {
        if( $create == true )
        {
            $array[$currentKey] = array();
        }
        else
        {
            return null;
        }
    }
    $array = &$array[$currentKey];

    /* if there are any keys left, move down one level */
    if( count( $arrayKeys ) > 0 && is_array( $array ) )
    {
        return getArrayValueReference( $array, $arrayKeys, $create );
    }

    return $array;
}
