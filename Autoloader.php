<?php

/**
 * A configurable class autoloader.
 * 
 * The default config-array is:
 * 
 * <code>
 * $configArray = array(
 * 
 * // comma-separated string or array
 * 'includeDirs' => 'lib/',
 * 
 * // directory to be skipped if it is the first one,
 * // may be also the first namespace-part, depending on 'useNamespaces'
 * 'skip' => null,
 * 
 * // comma-separated string or array of sprintf()-formats
 * 'filePattern' => '%s.php,%s.inc',
 * 
 * // use namespaces as directories, appended to the 'includeDirs'
 * // if set to true, this overrides 'classNameToDirDelimiter' with a backslash (\)
 * 'useNamespaces' => true,
 * 
 * // explode class name with 'classNameToDirDelimiter' to get directories;
 * 'classNameToDir' => false,
 * 
 * 'classNameToDirDelimiter' => '_',
 * 
 * // transform class name to lowercase
 * 'lowercase' => false,
 * 
 * );
 * </code>
 * 
 * @author Thomas Heuer <projekte@thomas-heuer.eu>
 */
class Autoloader
{
	private $classLoaderConfig;

	/**
	 * @param array $configArray will get merged with default config array
	 */
	public function __construct( $configArray = array() )
	{
		$this->setClassLoaderConfig( $configArray );

		/* register autoload function */
		spl_autoload_register( array( $this, 'classLoader' ), true, true );
	}

	/**
	 * @param array $configArray 
	 */
	private function setClassLoaderConfig( $configArray )
	{
		/* merge array into default configuration */
		$this->classLoaderConfig = array_merge( array(
			'includeDirs' => 'lib/',
			'skip' => null,
			'filePattern' => '%s.php,%s.inc',
			'useNamespaces' => true,
			'classNameToDir' => false,
			'classNameToDirDelimiter' => '_',
			'lowercase' => false,
		), ( array ) $configArray );
	}

	/**
	 * A configurable class autoloader.
	 * 
	 * @param string $className may use namespaces for folders
	 * @return boolean
	 * @throws \RuntimeException 
	 */
	private function classLoader( $className )
	{
		$includeDirs = $this->getArray( $this->classLoaderConfig['includeDirs'] );

		if( $this->classLoaderConfig['useNamespaces'] === false )
		{
			/* get rid of namespaces */
			$classNameAndNamespaces = explode( '\\', $className );
			$className = array_pop( $classNameAndNamespaces );
		}
		else
		{
			$this->classLoaderConfig['classNameToDir'] = true;
			$this->classLoaderConfig['classNameToDirDelimiter'] = '\\';
		}

		if( $this->classLoaderConfig['lowercase'] == true )
		{
			$className = strtolower( $className );
		}

		/* skip first part of class name, if specified */
		if( !is_null( $this->classLoaderConfig['skip'] ) && substr( $className, 0, strlen( $this->classLoaderConfig['skip'] ) ) == $this->classLoaderConfig['skip'] )
		{
			$className = substr( $className, strlen( $this->classLoaderConfig['skip'] ) );
		}

		if( $this->classLoaderConfig['classNameToDir'] == true )
		{
			$classDirs = explode( $this->classLoaderConfig['classNameToDirDelimiter'], $className );

			$className = array_pop( $classDirs ); // part after last delimiter as class name

			$classDirString = implode( '/', $classDirs );
			foreach( $includeDirs as &$directory )
			{
				if( empty( $classDirString ) )
				{
					continue;
				}

				$directory .= $classDirString . '/';
			}
		}

		// iterate over specified directories
		foreach( $includeDirs as $directory )
		{
			// iterate over specified file pattern
			foreach( $this->getArray( $this->classLoaderConfig['filePattern'] ) as $filePattern )
			{
				$fileName = sprintf( '%s%s', $directory, sprintf( $filePattern, $className ) );
				if( file_exists( $fileName ) )
				{
					require_once $fileName;
					return true;
				}
				else
				{
					/*
					 * Do not throw an exception here, or further autloader
					 * wont get called from the SPL __autoload stack.
					 */
					return false;
				}
			}
		}
	}

	/**
	 * Returns an array. If the value is scalar,
	 * it will get exploded by $delimiter.
	 * 
	 * This is a copy from the Utils class, to ensure
	 * the Autoloader could be used standalone.
	 * 
	 * @param mixed $value
	 * @param string $delimiter (default: ',')
	 * @return array
	 */
	private function getArray( $value, $delimiter = ',' )
	{
		// return arrays and objects as arrays
		if( !is_scalar( $value ) )
		{
			return ( array ) $value;
		}
		return explode( $delimiter, $value );
	}

}
