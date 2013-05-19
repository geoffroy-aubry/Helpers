# Helpers
[![Build Status](https://secure.travis-ci.org/geoffroy-aubry/Helpers.png?branch=stable)](http://travis-ci.org/geoffroy-aubry/Helpers)

Some helpers used in several personal packages.

## Description
Static methods of `Helpers` class:

* [arrayMergeRecursiveDistinct](#desc.arrayMergeRecursiveDistinct)  
* [exec](#desc.exec)  
* [flattenArray](#desc.flattenArray)  
* [intToMultiple](#desc.intToMultiple)  
* [numberFormat](#desc.numberFormat)  
* [isAssociativeArray](#desc.isAssociativeArray)  
* [round](#desc.round)  
* [stripBashColors](#desc.stripBashColors)  
* [strPutCSV](#desc.strPutCSV)  
* [ucwordWithDelimiters](#desc.ucwordWithDelimiters)  
* [utf8Encode](#desc.utf8Encode)  

<a name="desc.arrayMergeRecursiveDistinct"></a>
### arrayMergeRecursiveDistinct()
```php
/**
 * array_merge_recursive() does indeed merge arrays, but it converts values with duplicate
 * keys to arrays rather than overwriting the value in the first array with the duplicate
 * value in the second array, as array_merge does. I.e., with array_merge_recursive(),
 * this happens (documented behavior):
 *
 * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
 *     ⇒ array('key' => array('org value', 'new value'));
 *
 * arrayMergeRecursiveDistinct() does not change the datatypes of the values in the arrays.
 * Matching keys' values in the second array overwrite those in the first array, as is the
 * case with array_merge, i.e.:
 *
 * arrayMergeRecursiveDistinct(array('key' => 'org value'), array('key' => 'new value'));
 *     ⇒ array('key' => array('new value'));
 *
 * EVO on indexed arrays:
 *   Before:
 *     arrayMergeRecursiveDistinct(array('a', 'b'), array('c')) => array('c', 'b')
 *   Now:
 *     ⇒ array('c')
 *
 * @param array $aArray1
 * @param array $aArray2
 * @return array An array of values resulted from strictly merging the arguments together.
 * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
 * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
 * @author Geoffroy Aubry
 * @see http://fr2.php.net/manual/en/function.array-merge-recursive.php#89684
 */
public static function arrayMergeRecursiveDistinct (array $aArray1, array $aArray2);
```

<a name="desc.exec"></a>
### exec()
```php
/**
 * Executes the given shell command and returns an array filled with every line of output from the command.
 * Trailing whitespace, such as \n, is not included in this array.
 * On shell error (error code <> 0), throws a RuntimeException with error message..
 *
 * @param string $sCmd shell command
 * @param string $sOutputPath optional redirection of standard output
 * @return array array filled with every line of output from the command
 * @throws RuntimeException if shell error
 */
public static function exec ($sCmd, $sOutputPath='');
```

<a name="desc.flattenArray"></a>
### flattenArray()
```php
/**
 * Flatten a multidimensional array (keys are ignored).
 *
 * @param array $aArray
 * @return array a one dimensional array.
 * @see http://stackoverflow.com/a/1320156/1813519
 */
public static function flattenArray (array $aArray);
```
Example:
```php
$a = array(
    1, 
    'a' => array(
        'b' => array('c', 2), 
        'd'
    )
);
print_r(Helpers::flattenArray($a));
```
⇒
```php
Array(
    [0] => 1
    [1] => 'c'
    [2] => 2
    [3] => 'd'
)
```

<a name="desc.intToMultiple"></a>
### intToMultiple()
```php
/**
 * Returns specified value in the most appropriate unit, with that unit.
 * If $bBinaryPrefix is FALSE then use SI units (i.e. k, M, G, T),
 * else use IED units (i.e. Ki, Mi, Gi, Ti).
 * @see http://en.wikipedia.org/wiki/Binary_prefix
 *
 * @param int $iValue
 * @param bool $bBinaryPrefix
 * @return array a pair constituted by specified value in the most appropriate unit and that unit
 */
public static function intToMultiple ($iValue, $bBinaryPrefix=false);
```
Example:
```php
print_r(Helpers::intToMultiple(17825792, false));
print_r(Helpers::intToMultiple(17825792, true));
```
⇒
```php
Array(
    [0] => 17.825792
    [1] => 'M'
)
Array(
    [0] => 17
    [1] => 'Mi'
)
```

<a name="desc.numberFormat"></a>
### numberFormat()
```php
/**
 * Format a number with grouped thousands.
 * It is an extended version of number_format() that allows do not specify $decimals.
 *
 * @param float $fNumber The number being formatted.
 * @param string $sDecPoint Sets the separator for the decimal point.
 * @param string $sThousandsSep Sets the thousands separator. Only the first character of $thousands_sep is used.
 * @param int $iDecimals Sets the number of decimal points.
 * @return string A formatted version of $number.
 */
public static function numberFormat ($fNumber, $sDecPoint='.', $sThousandsSep=',', $iDecimals=null);
```

<a name="desc.isAssociativeArray"></a>
### isAssociativeArray()
```php
/**
 * Returns TRUE iff the specified array is associative.
 * Returns FALSE if the specified array is empty.
 *
 * http://stackoverflow.com/questions/173400/php-arrays-a-good-way-to-check-if-an-array-is-associative-or-sequential
 *
 * @param array $aArray
 * @return bool true ssi iff the specified array is associative
 */
public static function isAssociativeArray (array $aArray);
```

<a name="desc.round"></a>
### round()
```php
/**
 * Rounds specified value with precision $iPrecision as native round() function, but keep trailing zeros.
 *
 * @param float $fValue value to round
 * @param int $iPrecision the optional number of decimal digits to round to (can also be negative)
 * @return string
 */
public static function round ($fValue, $iPrecision=0);
```

<a name="desc.stripBashColors"></a>
### stripBashColors()
```php
/**
 * Remove all Bash color sequences from the specified string.
 *
 * @param string $sMsg
 * @return string specified string without any Bash color sequence.
 */
public static function stripBashColors ($sMsg);
```

<a name="desc.strPutCSV"></a>
### strPutCSV()
```php
/**
 * Formats a line passed as a fields array as CSV and return it, without the trailing newline.
 * Inspiration: http://www.php.net/manual/en/function.str-getcsv.php#88773
 *
 * @param array $aInput
 * @param string $sDelimiter
 * @param string $sEnclosure
 * @return string specified array converted into CSV format string
 */
public static function strPutCSV ($aInput, $sDelimiter = ',', $sEnclosure = '"');
```

<a name="desc.ucwordWithDelimiters"></a>
### ucwordWithDelimiters()
```php
/**
 * Returns a string with the first character of each word in specified string capitalized,
 * if that character is alphabetic.
 * Additionally, each character that is immediately after one of $aDelimiters will be capitalized too.
 *
 * @param string $sString
 * @param array $aDelimiters
 * @return string
 */
public static function ucwordWithDelimiters ($sString, array $aDelimiters=array());
```
Eaxmple:
```php
echo Helpers::ucwordWithDelimiters("hel-lo wo'rld", array('-', "'"));
```
⇒
```php
"Hel-Lo Wo'Rld"
```

<a name="desc.utf8Encode"></a>
### utf8Encode()
```php
/**
 * Returns the UTF-8 translation of the specified string, only if not already in UTF-8.
 *
 * @param string $s
 * @return string the UTF-8 translation of the specified string, only if not already in UTF-8.
 */
public static function utf8Encode ($s);
```

## Copyrights & licensing
Licensed under the GNU Lesser General Public License v3 (LGPL version 3).
See [LICENSE](https://github.com/geoffroy-aubry/Helpers/blob/stable/LICENSE) file for details.

## ChangeLog
See [CHANGELOG](https://github.com/geoffroy-aubry/Helpers/blob/stable/CHANGELOG.md) file for details.

## Running tests
To run the test suite, simply:

```bash
$ cp phpunit-dist.php phpunit.php    # and adapt, if necessary
$ phpunit -c phpunit.xml
```

## Git branching model
The git branching model used for development is the one described and assisted by `twgit` tool: [https://github.com/Twenga/twgit](https://github.com/Twenga/twgit).
