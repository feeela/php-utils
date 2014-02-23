<?php

/**
 * A countable iterator with additional magic functions.
 * 
 * This iterator uses the method names 'set', 'get', 'has', 'remove' for CRUD operations.
 * The method 'set' combines the insert and update commands.
 * Magic function wrappers are defined for those four methods. (See examples)
 * 
 * Example 1:
 * <code>
 * $ic = new IterableCountable();
 * $ic->set( 'key1', 'value1' );
 * $ic->set( 'key2', 'value2' );
 * echo count( $ic );
 * echo (int) $ic->has( 'key2' );
 * $ic->remove( 'key2' );
 * </code>
 * 
 * Example 2 – iteration:
 * <code>
 * foreach( $ic as $key => $value ) var_dump( $key, $value );
 * </code>
 * 
 * Example 3 – using magic functions:
 * <code>
 * $ic = new IterableCountable();
 * $ic->key1 = 'value1';
 * echo $ic->key1;
 * echo (int) isset( $ic->key1 );
 * unset( $ic->key1 );
 * </code>
 */
class IterableCountable implements \Iterator, \Countable
{
    protected $data = array();

    /**
     * @param string $key
     * @param mixed $value
     * @return boolean|\nippy\IterableCountable 
     */
    public function set( $key, $value )
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function get( $key )
    {
        return isset( $this->data[$key] ) ? $this->data[$key] : null;
    }

    /**
     * @param string $key
     * @return boolean
     */
    public function has( $key )
    {
        return isset( $this->data[$key] );
    }

    /**
     * @param string $key
     * @return \nippy\IterableCountable 
     */
    public function remove( $key )
    {
        unset( $this->data[$key] );
        return $this;
    }

    /**
     * @return array
     */
    public function getArray()
    {
        return $this->data;
    }

    /* methods from interface Iterator */

    /**
     * @return mixed
     */
    public function current()
    {
        return current( $this->data );
    }

    /**
     * @return scalar
     */
    public function key()
    {
        return key( $this->data );
    }

    /**
     * @return void
     */
    public function next()
    {
        next( $this->data );
    }

    /**
     * @return void
     */
    public function rewind()
    {
        reset( $this->data );
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        return key( $this->data ) !== null;
    }

    /* methods from interface Countable */

    /**
     * @return int 
     */
    public function count()
    {
        return count( $this->data );
    }

    /* magic wrapper */

    /**
     * wrapper for self::set()
     */
    public function __set( $key, $value )
    {
        return $this->set( $key, $value );
    }

    /**
     * wrapper for self::get()
     */
    public function __get( $key )
    {
        return $this->get( $key );
    }

    /**
     * wrapper for self::has()
     */
    public function __isset( $key )
    {
        return $this->has( $key );
    }

    /**
     * wrapper for self::remove()
     */
    public function __unset( $key )
    {
        return $this->remove( $key );
    }

}
