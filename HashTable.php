<?php

/**
 * HashTable
 * 
 * An iterator, using a SHA1 hash as key.
 * This iterator is able to use non-scalar values as key.
 */
class HashTable extends IterableCountable
{
	protected $binaryKey;

	/**
	 * @param boolean $binaryKey Store the key as binary (TRUE) or hexadecimal (FALSE) string
	 */
	public function __construct( $binaryKey = true )
	{
		$this->binaryKey = $binaryKey;
	}

	/**
	 * Returns a SHA1 hash in binary representation.
	 *
	 * Stored as PHP string with 20 chars. This matches
	 * 40 hex-values/bytes, which is the default return
	 * length of the hash('sha1') function.
	 * Use a BINARY(20) to store that key in a database.
	 * 
	 * Numeric strings as key would be equal to their respective PHP data types.
	 * Empty keys are (probably) duplicates (this depends on their JSON encoded representation).
	 *
	 * @param string|array $key single string or array of keys
	 * @return string
	 */
	protected function getHash( $key )
	{
		if( !is_scalar( $key ) )
		{
			$key = json_encode( $key );
		}
		return hash( 'sha1', $key, $this->binaryKey );
	}

	/**
	 * Return the un-hashed key when passing in the hash.
	 * 
	 * @param string $hash
	 * @return mixed
	 */
	public function getKey( $hash )
	{
		return isset( $this->data[$hash]['key'] ) ? $this->data[$hash]['key'] : null;
	}

	/**
	 * @param string|array $key
	 * @param mixed $value
	 * @return boolean
	 */
	public function set( $key, $value )
	{
		$hash = $this->getHash( $key );
		$this->data[$hash] = array( 'key' => $key, 'value' => $value );
		return $this;
	}

	/**
	 * @param string|array $key
	 * @return mixed|null
	 */
	public function get( $key )
	{
		$hash = $this->getHash( $key );
		return isset( $this->data[$hash] ) ? $this->data[$hash]['value'] : null;
	}

	/**
	 * @param string|array $key
	 * @return boolean
	 */
	public function has( $key )
	{
		return isset( $this->data[$this->getHash( $key )] );
	}

	/**
	 * @param string|array $key
	 * @return boolean
	 */
	public function remove( $key )
	{
		unset( $this->data[$this->getHash( $key )] );
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function current()
	{
		$value = current( $this->data );
		return $value['value'];
	}

	/**
	 * @return mixed
	 */
	public function key()
	{
		return key( $this->data );
	}

	/* helper methods */

	/**
	 * Returns the HashTable as an array.
	 * If any value is an HashTable too, it will get inserted to
	 * the output array recursively.
	 *
	 * @param boolean $recursive default: TRUE
	 * @return array
	 */
	public function toArray( $recursive = true )
	{
		$output = array();

		foreach( $this as $hash => $value )
		{
			if( $recursive == true && $value instanceof HashTable)
			{
				$value = $value->toArray();
			}

			$tmp = array(
				'hash_bin' => $this->binaryKey ? $hash : pack( 'H*' , $hash ),
				/* PHP 5.4: */
//				'hash_bin' => $this->binaryKey ? $hash : hex2bin( $hash ),
				'hash_hex' => $this->binaryKey ? bin2hex( $hash ) : $hash,
				'key' => $this->getKey( $hash ),
				'value' => $value
			);
			$output[$hash] = $tmp;
		}

		return $output;
	}

}
