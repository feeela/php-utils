<?php

/**
 * A configurable class autoloader.
 *
 * The default config-array is:
 * 
 * <code>
 * $configArray = array(
 *
 * // Necessary if the router is not stored in the same directory as e.g. src/;
 * 'baseDir' => '.',
 *
 * // Comma-separated string or array
 * 'includeDirs' => 'src,lib',
 *
 * // String to be skipped/trimmed from class name.
 * // Maybe also the first namespace-part, depending on 'useNamespaces'
 * 'skip' => null,
 *
 * // Comma-separated string or array, gets parsed with sprintf()
 * 'filePattern' => '%s.php,%s.inc',
 *
 * // Use namespaces as directories, appended to the 'includeDirs'
 * 'useNamespaces' => true,
 *
 * // Explode class name with 'classNameToDirDelimiter' to get directories;
 * // This is set to TRUE when 'useNamespaces' is TRUE
 * 'classNameToDir' => false,
 *
 * // This is overwritten with a backslash when 'useNamespaces' is TRUE
 * 'classNameToDirDelimiter' => '_',
 * 
 * // This could be a callback to map a classname to a specific filename;
 * // If a callback is set and if it returns something else then FALSE,
 * // the returned value will be used for require_once
 * 'resolveStaticClassName' => null,
 *
 * // Transform class names to lowercase
 * 'lowercase' => false,
 *
 * );
 * </code>
 * 
 * To use this autoloader create an instance of it:
 * <code>
 * new Autoloader( $configArray );
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
            'baseDir' => '.',
            'includeDirs' => 'src,lib',
            'skip' => null,
            'filePattern' => '%s.php,%s.inc',
            'useNamespaces' => true,
            'classNameToDir' => false,
            'classNameToDirDelimiter' => '_',
            'resolveStaticClassName' => null,
            'lowercase' => false,
                ), ( array ) $configArray );
    }

    /**
     * A configurable class autoloader.
     *
     * @param string $className may use namespaces for folders
     * @return boolean
     */
    private function classLoader( $className )
    {
        if( is_callable( $this->classLoaderConfig['resolveStaticClassName'] ) )
        {
            $staticClassname = $this->classLoaderConfig['resolveStaticClassName']( $className );
            if( false !== $staticClassname )
            {
                require_once $staticClassname;
                return true;
            }
        }

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
            $className = ltrim( substr( $className, strlen( $this->classLoaderConfig['skip'] ) ), '\\' );
        }

        if( $this->classLoaderConfig['classNameToDir'] == true )
        {
            $classDirs = explode( $this->classLoaderConfig['classNameToDirDelimiter'], $className );

            $className = array_pop( $classDirs ); // part after last delimiter as class name

            $classDirString = implode( DIRECTORY_SEPARATOR, $classDirs );
            if( !empty( $classDirString ) )
            {
                $includeDirs = array_map( function( $value ) use( $classDirString ) {
                    return $value . DIRECTORY_SEPARATOR . $classDirString . DIRECTORY_SEPARATOR;
                }, $includeDirs );
            }
        }

        /* iterate over specified directories */
        foreach( $includeDirs as $directory )
        {
            /* iterate over specified file pattern */
            foreach( $this->getArray( $this->classLoaderConfig['filePattern'] ) as $filePattern )
            {
                $realDirectory = realpath( $this->classLoaderConfig['baseDir'] . DIRECTORY_SEPARATOR . $directory ) . DIRECTORY_SEPARATOR;
                $fileName = $realDirectory . sprintf( $filePattern, $className );
                if( file_exists( $fileName ) )
                {
                    require_once $fileName;
                    return true;
                }
            }
        }
    }

    /**
     * Returns an array. If the value is scalar,
     * it will get exploded by $delimiter.
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
