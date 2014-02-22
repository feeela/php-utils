<?php

/**
 * This function generates a recurive HTML list from an array
 * or an iterable object. By default, it returns an unordered
 * list (UL). But you can change the templates through the $options
 * argument.
 * 
 * Options: (as a key-value array)
 * - outer_tpl' wrapper for all rows (default: '<ul>%s</ul>')
 * - inner_tpl' => '<li class="%2$s">%1$s</li>'
 *
 * @param Iterator $list
 * @param array $options
 * @return null 
 */
function getHtmlList( $list, array $options = array() )
{
    if( empty( $list ) || (!is_array( $list ) && !($list instanceof Iterator) ) )
    {
        return null;
    }

    $options = array_merge( array(
        'outer_tpl' => '<ul>%1$s</ul>',
        'inner_tpl' => '<li class="%2$s">%1$s</li>',
            ), $options );

    $html = '';

    foreach( $list as $key => $value )
    {
        if( is_array( $value ) || ($value instanceof Iterator) )
        {
            $value = getHtmlList( $value );
        }
        $html .= sprintf( $options['inner_tpl'], $value, $key );
    }

    return sprintf( $options['outer_tpl'], $html );
}
