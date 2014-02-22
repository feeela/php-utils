<?php

/**
 * Format a document title to be URL-safe.
 * This function supports transliteration of most western European
 * scripts and greek but may be extended for use with other scripts.
 * 
 * @staticvar array $replaceArray The character map used.
 * @param string Alias to be formatted
 * @param boolean $lowercase Transform alias to lowercase
 * @return string Safe alias
 */
function getAlias( $alias, $lowercase = false )
{
    /* strip HTML */
    $alias = strip_tags( $alias );

    /* convert all named HTML entities to numeric entities */
    $alias = html_entity_decode( $alias, ENT_QUOTES, 'UTF-8' );

    /* convert all numeric entities to their actual character */
    $alias = preg_replace_callback( '/&#x([0-9a-f]{1,7});/i', function( $matches ) {
        return chr( hexdec( $matches[1] ) );
    }, $alias );
    $alias = preg_replace_callback( '/&#([0-9]{1,7});/', function( $matches ) {
        return chr( $matches[1] );
    }, $alias );

    /* Transliterates a string to ASCII.
     * This char map is based on Textpatterns dumbDown() function; see:
     * http://textpattern.googlecode.com/svn/development/4.x/textpattern/lib/txplib_misc.php */
    static $replaceArray = array(
        '&' => 'und', '@' => '-at-', '–' => '-', '\'' => '',
        '€' => 'euro', '§' => 'paragraph', 'µ' => 'micro', '+' => 'und',
        'ß' => 'ss', 'ẞ' => 'SS', 'ſ' => 's',
        'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'Ae', 'Å' => 'Ae', 'Æ' => 'Ae', 'Ā' => 'A', 'Ą' => 'A', 'Ă' => 'A',
        'Ç' => 'C', 'Ć' => 'C', 'Č' => 'C', 'Ĉ' => 'C', 'Ċ' => 'C',
        'Ď' => 'D', 'Đ' => 'D',
        'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ē' => 'E', 'Ę' => 'E', 'Ě' => 'E', 'Ė' => 'E',
        'Ĝ' => 'G', 'Ğ' => 'G', 'Ġ' => 'G', 'Ģ' => 'G',
        'Ĥ' => 'H', 'Ħ' => 'H',
        'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ï' => 'I', 'Ī' => 'I', 'Ĩ' => 'I', 'Ĭ' => 'I', 'Į' => 'I',
        'İ' => 'I',
        'Ĳ' => 'J', 'Ĵ' => 'J',
        'Ķ' => 'K',
        'Ľ' => 'L', 'Ĺ' => 'L', 'Ļ' => 'L', 'Ŀ' => 'L',
        'Ñ' => 'N', 'Ñ' => 'N', 'Ń' => 'N', 'Ň' => 'N', 'Ņ' => 'N', 'Ŋ' => 'N',
        'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'Oe', 'Ø' => 'Oe', 'Ō' => 'O', 'Ő' => 'O', 'Ŏ' => 'O',
        'Œ' => 'Oe',
        'Ŕ' => 'R', 'Ř' => 'R', 'Ŗ' => 'R',
        'Ś' => 'S', 'Ş' => 'S', 'Ŝ' => 'S', 'Ș' => 'S',
        'Ť' => 'T', 'Ţ' => 'T', 'Ŧ' => 'T', 'Ț' => 'T',
        'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Û' => 'U', 'Ū' => 'U', 'Ü' => 'Ue', 'Ů' => 'U', 'Ű' => 'U', 'Ŭ' => 'U', 'Ũ' => 'U',
        'Ų' => 'U',
        'Ŵ' => 'W',
        'Ŷ' => 'Y', 'Ÿ' => 'Y',
        'Ź' => 'Z', 'Ż' => 'Z',
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'ae', 'å' => 'ae', 'ā' => 'a', 'ą' => 'a', 'ă' => 'a', 'æ' => 'ae',
        'ç' => 'c', 'ć' => 'c', 'č' => 'c', 'ĉ' => 'c', 'ċ' => 'c',
        'ď' => 'd', 'đ' => 'd',
        'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ē' => 'e', 'ę' => 'e', 'ě' => 'e', 'ĕ' => 'e', 'ė' => 'e',
        'ƒ' => 'f', 'ĝ' => 'g', 'ğ' => 'g', 'ġ' => 'g',
        'ģ' => 'g',
        'ĥ' => 'h', 'ħ' => 'h',
        'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ī' => 'i', 'ĩ' => 'i', 'ĭ' => 'i', 'į' => 'i', 'ı' => 'i',
        'ĳ' => 'j', 'ĵ' => 'j',
        'ķ' => 'k', 'ĸ' => 'k',
        'ł' => 'l', 'ľ' => 'l', 'ĺ' => 'l', 'ļ' => 'l', 'ŀ' => 'l',
        'ñ' => 'n', 'ń' => 'n', 'ň' => 'n', 'ņ' => 'n', 'ŉ' => 'n', 'ŋ' => 'n',
        'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'oe', 'ø' => 'oe', 'ō' => 'o', 'ő' => 'o', 'ŏ' => 'o', 'œ' => 'oe',
        'ŕ' => 'r', 'ř' => 'r', 'ŗ' => 'r',
        'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'ue', 'ū' => 'u', 'ů' => 'ue', 'ű' => 'u', 'ŭ' => 'u', 'ũ' => 'u', 'ų' => 'u',
        'ŵ' => 'w', 'ÿ' => 'y', 'ŷ' => 'y', 'ż' => 'z', 'ź' => 'z',
        'Α' => 'A', 'Ά' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Έ' => 'E', 'Ζ' => 'Z', 'Η' => 'I', 'Ή' => 'I',
        'Θ' => 'TH',
        'Ι' => 'I', 'Ί' => 'I', 'Ϊ' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => 'KS', 'Ο' => 'O', 'Ό' => 'O',
        'Π' => 'P', 'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Ύ' => 'Y', 'Ϋ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS',
        'Ω' => 'O', 'Ώ' => 'O', 'α' => 'a', 'ά' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'έ' => 'e', 'ζ' => 'z',
        'η' => 'i', 'ή' => 'i', 'θ' => 'th', 'ι' => 'i', 'ί' => 'i', 'ϊ' => 'i', 'ΐ' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm',
        'ν' => 'n', 'ξ' => 'ks', 'ο' => 'o', 'ό' => 'o', 'π' => 'p', 'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'ύ' => 'y',
        'ϋ' => 'y', 'ΰ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'o', 'ώ' => 'o',
        'C#' => 'c-sharp', 'C++' => 'c-plusplus', 'c#' => 'c-sharp', 'c++' => 'c-plusplus',
        '/' => '-',
    );
    $alias = strtr( $alias, $replaceArray );

    /* convert white-space to dash */
    $alias = preg_replace( '/\s+/', '-', $alias );
    /* strip remaining non-alphanumeric characters */
    $alias = preg_replace( '/[^\.%A-Za-z0-9-]/', '', $alias );
    /* convert multiple dashes to one */
    $alias = preg_replace( '/-+/', '-', $alias );
    /* trim excess */
    $alias = trim( $alias, '-' );
    
    if( $lowercase == true )
    {
        $alias = strtolower( $alias );
    }

    return $alias;
}
