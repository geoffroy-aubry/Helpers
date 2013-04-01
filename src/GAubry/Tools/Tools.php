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
     * Transforme un tableau multidimensionnel en un tableau à une seule dimension,
     * en ramenant toutes les feuilles au premier niveau.
     *
     * @param array $array
     * @return array tableau à une seule dimension.
     * @see http://stackoverflow.com/a/1320156/1813519
     */
    public static function flatten (array $array) {
        $return = array();
        array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
        return $return;
    }

    /**
     * Retourne la chaîne spécifiée, en l'encodant en UTF8 seulement si celle-ci ne l'était pas déjà.
     *
     * @param string $s
     * @return string la chaîne spécifiée, en l'encodant en UTF8 seulement si celle-ci ne l'était pas déjà.
     */
    public static function utf8_encode ($s)
    {
        return (utf8_encode(utf8_decode($s)) == $s ? $s : utf8_encode($s));
    }

    /**
     * Exécute la commande shell spécifiée et retourne la sortie découpée par ligne dans un tableau.
     * En cas d'erreur shell (code d'erreur <> 0), lance une exception incluant le message d'erreur.
     *
     * @param string $sCmd
     * @return array tableau indexé du flux de sortie shell découpé par ligne
     * @throws RuntimeException en cas d'erreur shell
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

    public static function stripBashColors ($sMsg)
    {
        return preg_replace('/\x1B\[([0-9]{1,2}(;[0-9]{1,2})?)?[m|K]/', '', $sMsg);
    }

    // Allow negative precision.
    public static function round ($fValue, $iPrecision=0)
    {
        $sPrintfPrecision = max(0, $iPrecision);
        return sprintf("%01.{$sPrintfPrecision}f", round($fValue, $iPrecision));
    }

    public static function ucwordWithDelimiters ($str, array $aDelimiters=array("'", '-')){
        $sReturn = ucwords(strtolower($str));
        foreach ($aDelimiters as $sDelimiter) {
            if (strpos($sReturn, $sDelimiter) !== false) {
                $sReturn = implode($sDelimiter, array_map('ucfirst', explode($sDelimiter, $sReturn)));
            }
        }
        return $sReturn;
    }

    public static function intToSI ($iValue)
    {
        $prefixes = array(12 => 'T', 9 => 'G', 6 => 'M', 3 => 'k', 0 => '');

        $m = 0;
        foreach ($prefixes as $multiple => $s) {
            if ($iValue >= pow(10, $multiple)) {
                $m = $multiple;
                break;
            }
        }

        //$decimals = ($m > 0 && $val/pow(10, $m) < 100 ? 1 : 0);
        $decimals = ($m === 0 ? 0 : 1);
        return array(round($iValue / pow(10, $m), $decimals), $prefixes[$m]);
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
     * Retourne un couple comprenant d'une part le nombre d'octets contenus dans la plus grande unité informatique
     * inférieure à la taille spécifiée, et d'autre part le nom de cette unité.
     *
     * Par exemple, si $iFileSize vaut 2000, alors le résultat sera : array(1024, 'Kio').
     *
     * @param int $iFileSize taille en octets à changer d'unité
     * @return array tableau (int, string) comprenant d'une part le nombre d'octets contenus dans la plus grande
     * unité inférieure à la taille spécifiée, et d'autre part le nom de cette unité.
     */
    public static function getFileSizeUnit ($iFileSize)
    {
        if ($iFileSize < 1024) {
            $iUnit = 1;
            $sUnit = 'o';
        } else if ($iFileSize < 1024*1024) {
            $iUnit = 1024;
            $sUnit = 'Kio';
        } else {
            $iUnit = 1024*1024;
            $sUnit = 'Mio';
        }
        return array($iUnit, $sUnit);
    }

    /**
     * Retourne un couple comprenant d'une part la taille spécifiée arrondie,
     * et d'autre part l'unité dans laquelle la taille a été arrondie.
     *
     * Le second paramètre, si <> de 0, permet de spécifier une taille de référence pour le calcul de l'unité.
     *
     * Par exemple :
     * (100, 0) => ('100', 'o')
     * (100, 2000000) => ('<1', 'Mio')
     * (200, 0) => ('2', 'Kio')
     *
     * @param int $iSize taille à convertir
     * @param int $iRefSize référentiel de conversion, si différent de 0
     * @return array un couple comprenant d'une part la taille spécifiée arrondie,
     * et d'autre part l'unité dans laquelle la taille a été arrondie.
     */
    public static function convertFileSize2String ($iSize, $iRefSize=0)
    {
        if ($iRefSize === 0) {
            $iRefSize = $iSize;
        }
        list($iUnit, $sUnit) = self::getFileSizeUnit($iRefSize);

        $sFileSize = round($iSize/$iUnit);
        if ($sFileSize == 0 && $iSize > 0) {
            $sFileSize = '<1';
        }
        return array($sFileSize, $sUnit);
    }

    public static function strPutCSV ($input, $delimiter = ',', $enclosure = '"') {
        // Open a memory "file" for read/write...
        $fp = fopen('php://temp', 'r+');
        // ... write the $input array to the "file" using fputcsv()...
        fputcsv($fp, $input, $delimiter, $enclosure);
        // ... rewind the "file" so we can read what we just wrote...
        rewind($fp);
        // ... read the entire line into a variable...
        $data = fgets($fp);
        // ... close the "file"...
        fclose($fp);
        // ... and return the $data to the caller, with the trailing newline from fgets() removed.
        return rtrim( $data, "\n" );
    }
}
