<?php

/**
 * KeyValueTable
 * 
 * This is a simple key-value storage that creates it own database table
 * to save the data to a MySQL database.
 * 
 * Requires a PDO object with access to a selected database.
 * The database user needs the rights to SHOW, CREATE, SELECT, INSERT and UPDATE.
 * Table column `value` is not nullable. If some function returns NULL,
 * the requested key was not set.
 * 
 * Any values that are not scalar (e.g. arrays, objects) will be stored as JSON encoded string.
 *
 * @example For usage examples see the \nippy\IterableCountable class documentation.
 */
class KeyValueTable extends IterableCountable
{
	/**
	 * @var PDO
	 */
	private $database;

	/**
	 * @var string the used table name
	 */
	protected $table;

	/**
	 *
	 * @param \PDO $database
	 * @param string $table Used as table name for this object; if empty a random sha1 hex will be used instead.
     * @param boolean $autoload Load all the available data from the the DB table into the instance on instantiation.
     */
	public function __construct( \PDO &$database, $table = null, $autoload = true )
	{
		$this->database = $database;

		if( !empty( $table ) )
		{
			$this->table = trim( $this->database->quote( $table ), '\'' );
		}
		else
		{
			$this->table = sha1( uniqid( mt_rand(), true ) );
		}

		/* check if table exists */
		$tableExistsStmt = $this->database->prepare( sprintf( 'SHOW TABLES LIKE \'%s\'', $this->table ) );
		$tableExistsStmt->execute();
		if( $tableExistsStmt->rowCount() <= 0 )
		{
			/* no, create it */
			$this->setup();
		}
		elseif( $autoload == true )
		{
			/* load all data from database into object */
			$this->load();
		}
	}

	/**
	 * Creates the Iterable equivalent as MySQL table,
	 * using `key` and `value` as columns, where `key` is the PRIMARY index.
	 * 
	 * @return boolean
	 */
	private function setup()
	{   
		$createStatement = 'CREATE TABLE `%s` (
			`key` VARCHAR( 255 ) NOT NULL,
			`value` BLOB NOT NULL,
			PRIMARY KEY ( `key` ) 
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;';

		return false !== $this->database->exec( sprintf( $createStatement, $this->table ) );
	}

	protected function load()
	{
		$selectSql = 'SELECT `key`, `value` FROM `%s`';
		$selectStmt = $this->database->prepare( sprintf( $selectSql, $this->table ) );
		if( $selectStmt->execute() && $selectStmt->rowCount() > 0 )
		{
			while( $row = $selectStmt->fetch( \PDO::FETCH_ASSOC ) )
			{
				parent::set( $row['key'], $row['value'] );
			}
		}
	}

	/**
	 * Returns the table name; useful if the table name was generated.
	 * 
	 * @return string
	 */
	public function getTable()
	{
		return $this->table;
	}

	/**
	 * Insert or update a value represented by a key.
	 * 
	 * @param string|int $key
	 * @param mixed $value
	 * @return boolean|\nippy\KeyValueTable
	 */
	public function set( $key, $value )
	{
		$insertUpdateSql = 'INSERT INTO `%s` (`key`, `value`)'
            . ' VALUES (:key, :value)'
            . ' ON DUPLICATE KEY UPDATE `value` = :update_value';
		$stmt = $this->database->prepare( sprintf( $insertUpdateSql, $this->table ) );

		$stmt->bindValue( ':key', $key );

        if( !is_scalar( $value ) )
        {
            $insertValue = 'json:' . json_encode( $value );
        }
		$stmt->bindValue( ':value', $insertValue );
		$stmt->bindValue( ':update_value', $insertValue );

		if( $stmt->execute() == true )
		{
			parent::set( $key, $value );
		}

		return $this;
	}

    public function get( $key )
    {
        $value = parent::get( $key );
        if( substr( $value, 0, 5 ) == 'json:' )
        {
            $value = json_decode( substr( $value, 5 ) );
        }
        return $value;
    }

    /**
	 * unset()
	 * 
	 * @param string|int $key
	 * @return boolean
	 */
	public function remove( $key )
	{
		$deleteSql = 'DELETE FROM `%s` WHERE `key` = :key';
		$deleteStmt = $this->database->prepare( sprintf( $deleteSql, $this->table ) );
		$deleteStmt->bindValue( ':key', $key );

		if( $deleteStmt->execute() && $deleteStmt->rowCount() > 0 )
		{
			parent::remove( $key );
		}

		return $this;
	}

}
