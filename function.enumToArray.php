<?php

/**
 * Retrieve ENUM-values from a MySQL-column
 *
 * @param \PDO database
 * @param string table
 * @param string column
 * @author based on: http://barrenfrozenwasteland.com/index.php?q=node/7
 * @return array ENUM-values (or NULL on error)
 */
function enumToArray( \PDO $database, $table, $column )
{
    /* PDO doesn't allow parameter binding in prepared statements for table- and column-names */
    $enumStmt = $database->query( 'SHOW COLUMNS FROM ' . trim( $database->quote( $table ), '\'' ) . ' LIKE ' . $database->quote( $column ) );
    if( $enumStmt->rowCount() <= 0 )
    {
        return null;
    }
    $resultRow = $enumStmt->fetch( \PDO::FETCH_ASSOC );
    $enumStmt->closeCursor();
    $enumOptions = array();
    preg_match_all( "/'([\w ]*)'/", $resultRow['type'], $enumOptions );
    return $enumOptions[1];
}
