<?php

/**
 * Lexicon Class
 * singleton class for handling strings in different languages via a lexicon-database
 *
 * CAUTION: everything should be UTF-8 to work properly
 * 		- SQL-tables via collation
 * 		- database connection, e.g. MySQL: "SET NAMES utf8" as first statement
 * 		- HTML via META-tag or HTTP-header
 * 
 * Example:
 * <code>
 * $database = new PDO('mysql:host=localhost;port=3306;dbname=lexicon;', 'user', 'password', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8') );
 * 
 * // $language is an IETF language tag, following BCP 47
 * $language = !empty($_REQUEST['language']) ? $_REQUEST['language'] : 'en';
 * 
 * // include class
 * require_once('Lexicon.php');
 * 
 * // set fallback language (see function-doc) 	
 * Lexicon::setFallbackLanguage('en');
 * 
 * // get lexicon instance, using the  PDO-object and language tag
 * $l = Lexicon::_getLexiconInstance($database, $language);
 * 
 * // example calls
 * echo $l->search('search');
 * echo $l->search('not_found');
 * echo $l->login('login');
 * echo $l->login('min_username_length', array('charcount' => 4));
 * 
 * echo $l;
 * </code>
 *
 * @author Thomas Heuer <projekte@thomas-heuer.eu>
 * @version 05.10.2010
 * @license Creative Commons Attribution-ShareAlike 3.0 Unported (CC BY-SA 3.0) http://creativecommons.org/licenses/by-sa/3.0/
 */
class Lexicon
{
    private static $lexInstance;
    private $_database;
    private $_lexicon;
    private static $_fallbackLanguage;
    private $_language;
    private $_preparedStatement;
    public $sortorder;

    /**
     * this is a singleton class, hence a private constructor
     * @param PDO db
     * @param string language IETF-language-code
     * @param mixed namespace string or array of string with namspaces to load
     */
    public static function _getLexiconInstance( $db, $language, $namespace = 'core' )
    {
        if( !isset( self::$lexInstance ) )
        {
            self::$lexInstance = new Lexicon( $db, $language, $namespace );
        }
        return self::$lexInstance;
    }

    /**
     * if an entry doesn't exist in currently selected language,
     * this class returns the string in a specified fallback language;
     * every entry must at least exist in that fallback language, default is 'en';
     * 
     * this function should be executed before _getLexiconInstance(), if the default-language isn't english
     */
    public static function setFallbackLanguage( $language )
    {
        self::$_fallbackLanguage = $language;
    }

    /**
     * @return string currently used IETF-language-code
     */
    public function _getCurrentLanguage()
    {
        return $this->_language;
    }

    /**
     * save new entry to database
     */
    public function _updateLexicon( $language, $namespace, $key, $value, $comment = null )
    {
        try
        {
            $syncStmt = $this->_database->prepare( 'INSERT INTO `lexicon` (`lang`, `namespace`, `key`, `value`, `comment`)'
                    . ' VALUES (:language, :namespace, :key, :value, :comment)'
                    . ' ON DUPLICATE KEY UPDATE `value` = :update_value, `comment` = :update_comment' );
            $syncStmt->bindValue( ':language', $language );
            $syncStmt->bindValue( ':namespace', $namespace );
            $syncStmt->bindValue( ':key', $key );
            $syncStmt->bindValue( ':value', $value );
            $syncStmt->bindValue( ':comment', $comment );
            $syncStmt->bindValue( ':update_value', $value );
            $syncStmt->bindValue( ':update_comment', $comment );
            $syncStmt->execute();
            if( $syncStmt->rowCount() > 0 )
            {
                return $syncStmt->rowCount();
            }
        }
        catch( PDOException $e )
        {
            return false;
        }
        return false;
    }

    /**
     * shorthand-notation for __call()
     * used to retrieve entries from 'core'-namespace;
     * usage: $langObj->requestedKey;
     */
    public function __get( $key )
    {
        return $this->_entry( $key );
    }

    /**
     * Get single lexicon entry by namespace and key
     * Usage: $langObj->requestedNamespace(requestedKey, placeholderValues);
     *
     * @param string namespace current namespace
     * @param array params array of namespace and a optional associative array of placeholder and values
     * @return string the translation; NULL if $key doesn't exist in current namespace
     */
    public function __call( $namespace, $params )
    {
        if( empty( $params ) )
        {
            return null;
        }

        if( isset( $params[1] ) )
        {
            return $this->_entry( $params[0], $namespace, $params[1] );
        }
        else
        {
            return $this->_entry( $params[0], $namespace );
        }
    }

    public function __toString()
    {
        $output = "loaded lexicon namespaces:\n";
        $total = 0;
        foreach( array_keys( $this->_lexicon ) as $namespace )
        {
            $no = count( $this->_lexicon[$namespace] );
            $output .= '- ' . $namespace . ' (' . $no . " entries)\n";
            $total += $no;
        }
        return $output . $total . " entries total.\n";
    }

    /**
     * load a namepsace
     * @param mixed namespace string or array of string with namspaces to load
     * @param bool core load core namespace (optional, default = false)
     * @param string $sortorder
     */
    public function _loadNamespace( $namespace, $core = false, $sortorder = null )
    {
        if( !isset( self::$_fallbackLanguage ) )
        {
            self::$_fallbackLanguage = 'en';
        }

        if( !isset( $this->_preparedStatement ) || !is_null( $sortorder ) )
        {
            $sortorder = !is_null( $sortorder ) ? $sortorder : $this->sortorder;
            $this->_preparedStatement = $this->_database->prepare( 'SELECT l1.`key`, l1.`value` as `fallback_language_value`, l2.`value`'
                    . ' FROM `lexicon` l1 LEFT JOIN `lexicon` l2 ON l1.`namespace` = l2.`namespace` AND l1.`key`= l2.`key` AND l2.`lang` = :language'
                    . ' WHERE l1.`lang` = :fallbackLanguage'
                    . ' AND l1.`namespace` = :namespace ' . $sortorder );
        }

        if( !is_array( $namespace ) )
        {
            if( false !== strpos( $namespace, ',' ) )
            {
                $namespace = explode( ',', $namespace );
            }
            else
            {
                $namespace = ( array ) $namespace;
            }
        }

        if( !in_array( 'core', $namespace ) && (!isset( $this->_lexicon['core'] ) || ($core == true) ) )
        {
            array_unshift( $namespace, 'core' );
        }

        for( $i = 0; $i < count( $namespace ); $i++ )
        {
            $this->_preparedStatement->bindValue( ':language', $this->_language );
            $this->_preparedStatement->bindValue( ':fallbackLanguage', self::$_fallbackLanguage );
            $this->_preparedStatement->bindValue( ':namespace', $namespace[$i] );
            if( $this->_preparedStatement->execute() )
            {
                foreach( $this->_preparedStatement->fetchAll( PDO::FETCH_ASSOC ) as $row )
                {
                    $this->_lexicon[$namespace[$i]][$row['key']] = !is_null( $row['value'] ) ? $row['value'] : $row['fallback_language_value'];
                }
            }
        }
    }

    /**
     * Retrieve namespaces as array
     * 
     * @return array
     */
    public function _getAllNamespaces()
    {
        try
        {
            $result = $this->_database->query( 'SELECT `namespace` FROM `lexicon` GROUP BY `namespace` ORDER BY `namespace`' );
            return $result->fetchAll( PDO::FETCH_COLUMN, 0 );
        }
        catch( PDOException $e )
        {
            return array();
        }
    }

    /**
     * Retrieve all entries for given namespace as array.
     */
    public function _getNamespace( $namespace, $sortorder = null )
    {
        if( empty( $this->_lexicon[$namespace] ) )
        {
            $this->_loadNamespace( $namespace, false, $sortorder );
        }
        return $this->_lexicon[$namespace];
    }

    /* private functions */

    /**
     * This is a singleton class, hence a private constructor, use Lexicon::_getLexiconInstance() instead.
     * 
     * @param PDO $database
     * @param string language IETF-language-code
     * @param mixed $namespace string or array of string with namspaces to load
     */
    private function __construct( PDO $database, $language, $namespace )
    {
        $this->_database = $database;
        $this->sortorder = 'ORDER BY `key`';

        $this->_language = $language;
        foreach( $namespace = ( array ) $namespace as $value )
        {
            $this->_loadNamespace( $value, true );
        }
    }

    /**
     * Get single lexicon entry by key and namespace
     * 
     * @param string key name of translation-variable
     * @param string namespace current namespace, default: 'core'
     * @param array params associative array of placeholder and values
     * @return string the translation; NULL if $key doesn't exist in current namespace
     */
    private function _entry( $key, $namespace = 'core', $params = array() )
    {
        /* load namespace if necessary */
        if( !isset( $this->_lexicon[$namespace] ) )
        {
            $this->_loadNamespace( $namespace, false );
        }

        if( isset( $this->_lexicon[$namespace][$key] ) )
        {
            return $this->_parse( $this->_lexicon[$namespace][$key], $params );
        }
        else
        {
            /**
             * @todo The commented-out part should be used for live version, or just return NULL
             */
            return '<span title="missing lexicon entry" style="color: #c00;">{' . strtolower( $namespace ) . '-&gt;' . strtolower( $key ) . '}</span>';
//			return '<!-- missing {' . strtolower($namespace) . '-&gt;' . strtolower($key) . '} -->';
        }
    }

    /**
     * replace template-variables
     */
    private function _parse( $str, $params )
    {
        if( empty( $str ) )
        {
            return '';
        }

        if( empty( $params ) )
        {
            return $this->_cleanupOutput( $str );
        }

        foreach( $params as $key => $value )
        {
            $str = str_ireplace( '{' . $key . '}', $value, $str );
        }
        return $this->_cleanupOutput( $str );
    }

    private function _cleanupOutput( $output )
    {
        return preg_replace( '/\{(.*?)\}/', '', $output );
    }

    private function __clone()
    {
        trigger_error( 'Clone is not allowed.', E_USER_ERROR );
    }

}
