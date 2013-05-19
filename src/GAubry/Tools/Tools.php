<?php

namespace GAubry\Tools;

/**
 * Outils divers et variés...
 */
class Tools
{
    private function __construct()
    {
    }

    /**
     * Flatten a multidimensional array (keys are ignored).
     *
     * @param array $array
     * @return array tableau à une seule dimension.
     * @see http://stackoverflow.com/a/1320156/1813519
     */
    public static function flattenArray (array $aArray) {
        $aFlattened = array();
        array_walk_recursive($aArray, function($a) use (&$aFlattened) {$aFlattened[] = $a;});
        return $aFlattened;
    }

    /**
     * Returns the UTF-8 translation of the specified string, only if not already in UTF-8.
     *
     * @param string $s
     * @return string the UTF-8 translation of the specified string, only if not already in UTF-8.
     */
    public static function utf8Encode ($s)
    {
        return (utf8_encode(utf8_decode($s)) == $s ? $s : utf8_encode($s));
    }

    /**
     * Executes the given shell command and returns an array filled with every line of output from the command.
     * Trailing whitespace, such as \n, is not included in this array.
     * On shell error (error code <> 0), throws a RuntimeException with error message..
     *
     * @param string $sCmd shell command
     * @return array array filled with every line of output from the command
     * @throws RuntimeException if shell error
     */
    public static function exec ($sCmd, $sOutputPath='')
    {
        if (empty($sOutputPath)) {
            $sFullCmd = '( ' . $sCmd . ' ) 2>&1';
        } else {
            $sFullCmd = "( $sCmd ) 1>$sOutputPath 2>&1 & echo $!";
        }
        exec($sFullCmd, $aResult, $iReturnCode);
        if ($iReturnCode !== 0) {
            throw new RuntimeException(
                "Exit code not null: $iReturnCode. Result: '" . implode("\n", $aResult) . "'",
                $iReturnCode
            );
        }
        return $aResult;
    }

    /**
     * Remove all Bash color sequences from the specified string.
     *
     * @param string $sMsg
     * @return string specified string without any Bash color sequence.
     */
    public static function stripBashColors ($sMsg)
    {
        return preg_replace('/\x1B\[([0-9]{1,2}(;[0-9]{1,2}){0,2})?[m|K]/', '', $sMsg);
    }

    /**
     * Rounds specified value with precision $iPrecision as native round() function,
     * but keep trailing zero.
     *
     * @param float $fValue value to round
     * @param int $iPrecision the optional number of decimal digits to round to (can also be negative)
     * @return string
     */
    public static function round ($fValue, $iPrecision=0)
    {
        $sPrintfPrecision = max(0, $iPrecision);
        return sprintf("%01.{$sPrintfPrecision}f", round($fValue, $iPrecision));
    }

    /**
     * Returns a string with the first character of each word in specified string capitalized,
     * if that character is alphabetic.
     * Additionally, each character that is immediately after one of $aDelimiters will be capitalized too.
     *
     * @param string $sString
     * @param array $aDelimiters
     * @return string
     */
    public static function ucwordWithDelimiters ($sString, array $aDelimiters=array())
    {
        $sReturn = ucwords($sString);
        foreach ($aDelimiters as $sDelimiter) {
            if (strpos($sReturn, $sDelimiter) !== false) {
                $sReturn = implode($sDelimiter, array_map('ucfirst', explode($sDelimiter, $sReturn)));
            }
        }
        return $sReturn;
    }

    /**
     * Returns specified value in the most appropriate unit with that unit.
     * If $bBinaryPrefix is FALSE then use SI units (i.e. k, M, G, T),
     * else use IED units (i.e. Ki, Mi, Gi, Ti).
     * @see http://en.wikipedia.org/wiki/Binary_prefix
     *
     * @param int $iValue
     * @param bool $bBinaryPrefix
     * @return array a pair constituted by specified value in the most appropriate unit and that unit
     */
    public static function intToMultiple ($iValue, $bBinaryPrefix=false)
    {
        static $aAllPrefixes = array(
            10 => array(12 => 'T', 9 => 'G', 6 => 'M', 3 => 'k', 0 => ''),
            2 => array(40 => 'Ti', 30 => 'Gi', 20 => 'Mi', 10 => 'Ki', 0 => ''),
        );

        $iBase = ($bBinaryPrefix ? 2 : 10);
        $aPrefixes = $aAllPrefixes[$iBase];
        $m = 0;
        foreach (array_keys($aPrefixes) as $iMultiple) {
            if ($iValue >= pow($iBase, $iMultiple)) {
                $m = $iMultiple;
                break;
            }
        }

        return array($iValue / pow($iBase, $m), $aPrefixes[$m]);
    }

    /**
     * Format a number with grouped thousands.
     * It is an extended version of number_format() that allow do not specify $decimals.
     *
     * @param float $fNumber The number being formatted.
     * @param string $sDecPoint Sets the separator for the decimal point.
     * @param string $sThousandsSep Sets the thousands separator. Only the first character of $thousands_sep is used.
     * @param int $iDecimals Sets the number of decimal points.
     * @return string A formatted version of $number.
     */
    public static function numberFormat ($fNumber, $sDecPoint='.', $sThousandsSep=',', $iDecimals=NULL)
    {
        if ($iDecimals !== NULL) {
            return number_format($fNumber, $iDecimals, $sDecPoint, $sThousandsSep);
        } else {
            $tmp = explode('.', $fNumber);
            $out = number_format($tmp[0], 0, $sDecPoint, $sThousandsSep);
            if (isset($tmp[1])) {
                $out .= $sDecPoint.$tmp[1];
            }
            return $out;
        }
    }

    /**
     * Formats a line passed as a fields array as CSV and return it, without the trailing newline.
     * Inspiration: http://www.php.net/manual/en/function.str-getcsv.php#88773
     *
     * @param array $aInput
     * @param string $sDelimiter
     * @param string $sEnclosure
     * @return string specified array converted into CSV format string
     */
    public static function strPutCSV ($aInput, $sDelimiter = ',', $sEnclosure = '"') {
        // Open a memory "file" for read/write...
        $fp = fopen('php://temp', 'r+');
        fputcsv($fp, $aInput, $sDelimiter, $sEnclosure);
        // ... rewind the "file" so we can read what we just wrote...
        rewind($fp);
        $sData = fgets($fp);
        fclose($fp);
        // ... and return the $data to the caller, with the trailing newline from fgets() removed.
        return rtrim($sData, "\n");
    }

    /**
     * array_merge_recursive() does indeed merge arrays, but it converts values with duplicate
     * keys to arrays rather than overwriting the value in the first array with the duplicate
     * value in the second array, as array_merge does. I.e., with array_merge_recursive(),
     * this happens (documented behavior):
     *
     * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('org value', 'new value'));
     *
     * arrayMergeRecursiveDistinct() does not change the datatypes of the values in the arrays.
     * Matching keys' values in the second array overwrite those in the first array, as is the
     * case with array_merge, i.e.:
     *
     * arrayMergeRecursiveDistinct(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('new value'));
     *
     * EVO sur sous-tableaux indexés :
     *   Avant :
     *     array_merge_recursive_distinct(array('a', 'b'), array('c')) => array('c', 'b')
     *   Maintenant :
     *     => array('c')
     *
     * @param array $aArray1
     * @param array $aArray2
     * @return array An array of values resulted from strictly merging the arguments together.
     * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
     * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
     * @author Geoffroy Aubry
     */
    public static function arrayMergeRecursiveDistinct (array $aArray1, array $aArray2) {
        $aMerged = $aArray1;
        if (self::isAssociativeArray($aMerged)) {
            foreach ($aArray2 as $key => &$value) {
                if (is_array($value) && isset($aMerged[$key]) && is_array($aMerged[$key])) {
                    $aMerged[$key] = self::arrayMergeRecursiveDistinct($aMerged[$key], $value);
                } else {
                    $aMerged[$key] = $value;
                }
            }
        } else  {
            $aMerged = $aArray2;
        }
        return $aMerged;
    }

    /**
     * Returns TRUE iff the specified array is associative.
     * Returns FALSE if the specified array is empty.
     *
     * http://stackoverflow.com/questions/173400/php-arrays-a-good-way-to-check-if-an-array-is-associative-or-sequential
     *
     * @param array $aArray
     * @return bool true ssi iff the specified array is associative
     */
    public static function isAssociativeArray (array $aArray) {
        foreach (array_keys($aArray) as $key) {
            if ( ! is_int($key)) {
                return true;
            }
        }
        return false;
    }
}
