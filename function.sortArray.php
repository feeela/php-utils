<?php

/**
 * Sort 2D-associative array by one $field. (ASC)
 * 
 * $array = sortArray( $array, 'column_name' );
 * 
 * @param array $data
 * @param int|string $field
 * @return array
 * @version PHP 5.3
 */
function sortArray( $data, $field )
{
    uasort( $data, function($a, $b) use($field) {
        $retval = 0;
        if( $retval == 0 )
        {
            $retval = strnatcmp( $a[$field], $b[$field] );
        }
        return $retval;
    } );
    return $data;
}

/**
 * Sort 2D-associative array by one or many $fields. (ASC and DESC)
 * Extension of getArray(), to allow sorting to multiple columns.
 * 
 * @todo test this function under heavy load/with huge arrays;
 * 
 * Example:
 * <code>
 * $data = array(
 *     array("firstname" => "Mary", "lastname" => "Johnson", "age" => 25),
 *     array("firstname" => "Sarah", "lastname" => "Miller", "age" => 24),
 *     array("firstname" => "Patrick", "lastname" => "Miller", "age" => 27),
 *     array("firstname" => "Michael", "lastname" => "Davis", "age" => 43),
 * );
 * 
 * $data = sortArray($data, 'age');
 * $data = sortArrayMultiple($data, 'age'); // calls sortArray($data, 'age');
 * $data = sortArrayMultiple($data, array('lastname', 'firstname'));
 * $data = sortArrayMultiple($data, array(array('lastname', 'DESC'), 'firstname'));
 * </code>
 * 
 * @param array $data Input-array
 * @param string|array $fields Array-keys
 * @return array
 * @version PHP 5.3
 */
function sortArrayMultiple( $data, $fields )
{
    if( is_scalar( $fields ) )
    {
        return self::sortArray( $data, $fields );
    }

    $field = (array) $field;
    $direction = 1;
    uasort( $data, function($a, $b) use($fields) {
        $retval = 0;
        foreach( $fields as $fieldname )
        {
            if( is_array( $fieldname ) )
            {
                $fieldname = key( $fieldname );
                $direction = 'DESC' == value( $fieldname ) ? -1 : 1;
            }

            if( $retval == 0 )
            {
                $retval = $direction * strnatcmp( $a[$fieldname], $b[$fieldname] );
            }
        }
        return $retval;
    } );
    return $data;
}
