<?php

/**
 * Read a CSV file, given an open resource handle.
 *
 * @param resource $handle
 * @param boolean $firstRowContainsHeadings Choose to either use the first row as headings or use no headings at all.
 * @param int|boolean $identifierColumnNumber (optional) Zero-based number for the column that holds ID's (default: boolean FALSE to use the standard numeric index)
 * @param array $overwriteHeadings (optional) The values will be used as headings. If there are fewer array entries than columns in the CSV, the rest will get numeric keys.
 * @return array
 */
function readCsv( $handle, $firstRowContainsHeadings = true, $identifierColumnNumber = false, $overwriteHeadings = null )
{
    /* alist of returned artist items */
    $rows = array();

    /* set up array label/table headers */
    $header = array();
    if( $firstRowContainsHeadings == true )
    {
        /* get CSV header (first line) */
        $header = fgetcsv( $handle );
    }
    if( is_array( $overwriteHeadings ) && !empty( $overwriteHeadings ) )
    {
        /* set manual headings */
        $header = $overwriteHeadings;
    }

    /* iterate over all rows in the CSV and put each row into an array entry */
    while( ($data = fgetcsv( $handle )) !== false )
    {
        $columns = array();

        foreach( $data as $i => $column )
        {
            $columns[(isset( $header[$i] ) ? $header[$i] : $i)] = $column;
        }

        if( $identifierColumnNumber === false )
        {
            $rows[] = $columns;
        }
        else
        {
            if( $data[$identifierColumnNumber] )
            {
                $rows[$data[$identifierColumnNumber]] = $columns;
            }
        }
    }
    return $rows;
}

/**
 * Write data to a CSV file.
 * 
 * @todo Do not use arrayToCsv() but write each line instead of a single string to be able to handle large amounts of data.
 *
 * @param array $data array of associative arrays
 * @param string $filename The desired filename, a date will be appended by default
 * @param string $path Will get used as path as output directory
 * @param boolean $appendDate (optional) Toggles appending date string to filename (defaults to true)
 * @return string Returns the filename that was written into or FALSE on error.
 * @depends arrayToCsv()
 */
function writeCsv( array $data, $filename, $path = null, $appendDate = true )
{
    if( empty( $data ) )
    {
        return false;
    }

    /* display field/column names as first row */
    $csvOutput = arrayToCsv( $data );

    /* create path and filename */
    if( $appendDate === true )
    {
        $filename = sprintf( '%s/%s.%s.csv', realpath( $path ), $filename, date( 'Y-m-d' ) );
    }
    else
    {
        $filename = sprintf( '%s/%s.csv', realpath( $path ), $filename );
    }

    if( false !== file_put_contents( $filename, $csvOutput ) )
    {
        return $filename;
    }

    return false;
}
