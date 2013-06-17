<?php

namespace GAubry\Helpers;

/**
 * Outils divers et variés...
 */
class Debug
{

    public static $sDisplayPatterns = array(
        'html' => '<pre><i>[function %1$s() in file %2$s, line %3$s]</i>\n<b>%4$s</b> = ',
        'cli'  => <<<EOT
\033[2;33;40m[function \033[1m%1\$s\033[2m in file \033[1m%2\$s\033[2m, line \033[1m%3\$s\033[2m]
\033[1m%4\$s\033[2m = \033[0m

EOT
    );

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * This function will return the name string of the function that called $function.
     * To return the caller of your function, either call get_caller(), or get_caller(__FUNCTION__).
     *
     * @see http://stackoverflow.com/a/4767754
     * @author Aram Kocharyan
     * @author Geoffroy Aubry
     */
    public static function getCaller ($function=NULL, $use_stack=NULL)
    {
        if (is_array($use_stack)) {
            // If a function stack has been provided, used that.
            $stack = $use_stack;
        } else {
            // Otherwise create a fresh one.
            $stack = debug_backtrace();
        }

        if ($function == NULL) {
            // We need $function to be a function name to retrieve its caller. If it is omitted, then
            // we need to first find what function called get_caller(), and substitute that as the
            // default $function. Remember that invoking get_caller() recursively will add another
            // instance of it to the function stack, so tell get_caller() to use the current stack.
            list($function, , ) = self::getCaller(__FUNCTION__, $stack);
        }

        if (is_string($function) && $function != '') {
            // If we are given a function name as a string, go through the function stack and find
            // it's caller.
            for ($i = 0; $i < count($stack); $i++) {
                $curr_function = $stack[$i];
                // Make sure that a caller exists, a function being called within the main script won't have a caller.
                if ($curr_function['function'] == $function && ($i + 1) < count($stack)) {
                    if (preg_match("/^(.*?)\((\d+)\) : eval\(\)\\'d code$/i", $stack[$i]['file'], $aMatches) === 1) {
                        return array('eval', $aMatches[1], $aMatches[2]);
                    } else {
// preg_match("/^(.*?)\((\d+)\) : eval\(\)\\'d code$/i", $stack[$i]['file'], $aMatches);
// var_dump('×', $stack[$i + 1]['function'], $stack[$i]['file'], $stack[$i]['line'], $aMatches, '|');
                        return array($stack[$i + 1]['function'], $stack[$i]['file'], $stack[$i]['line']);
                    }
                }
            }
        }

        // If out of any function:
        if ($curr_function['function'] == $function) {
            return array('', $curr_function['file'], $curr_function['line']);
        } else {
            // At this stage, no caller has been found, bummer.
            return array();
        }
    }

    /**
     * Return the name of the first parameter of the penultimate function call.
     *
     * TODO bug si plusieurs appels sur la même ligne…
     *
     * @return string the name of the first parameter of the penultimate function call.
     * @see http://stackoverflow.com/a/6837836
     * @author Sebastián Grignoli
     * @author Geoffroy Aubry
     */
    private static function getVarName ($sFunction, $sFile, $iLine)
    {
        $src = file($sFile);
        $line = $src[$iLine - 1];
        preg_match("#$sFunction\s*\((.+)\)#", $line, $match);

        /* let's count brackets to see how many of them actually belongs
         to the var name
        Eg:   die(catch_param($this->getUser()->hasCredential("delete")));
        We want:       $this->getUser()->hasCredential("delete")
        */
        $max = strlen($match[1]);
        $varname = '';
        $c = 0;
        for($i = 0; $i < $max; $i++) {
            if ($match[1]{$i} == '(' ) {
                $c++;
            } elseif ($match[1]{$i} == ')') {
                $c--;
                if ($c < 0) {
                    break;
                }
            }
            $varname .= $match[1]{$i};
        }

        // $varname now holds the name of the passed variable ('$' included)
        // Eg:   catch_param($hello)
        //             => $varname = "$hello"
        // or the whole expression evaluated
        // Eg:   catch_param($this->getUser()->hasCredential("delete"))
        //             => $varname = "$this->getUser()->hasCredential(\"delete\")"

        return $varname;
    }

    private static function displayTitle ($sPattern)
    {
        list($sDebugFunction, , ) = self::getCaller();
        list($sFunction, $sFile, $sLine) = self::getCaller($sDebugFunction);
        $sFunction = (empty($sFunction) ? '∅' : "$sFunction()");
        $sVarName = self::getVarName($sDebugFunction, $sFile, $sLine);
        echo sprintf(self::$sDisplayPatterns[$sPattern], $sFunction, $sFile, $sLine, $sVarName);
    }

    public static function htmlVarDump ($mValue)
    {
        ob_start();
        var_dump($mValue);
        $sOut = ob_get_contents();
        ob_end_clean();

        self::displayTitle('html');
        echo htmlspecialchars($sOut, ENT_QUOTES);
        echo '</pre>';
    }

    public static function htmlPrintr ($mValue)
    {
        self::displayTitle('html');
        echo htmlspecialchars(print_r($mValue, true), ENT_QUOTES);
        echo '</pre>';
    }

    public static function varDump ($mValue)
    {
        self::displayTitle('cli');
        var_dump($mValue);
        echo PHP_EOL;
    }

    public static function printr ($mValue)
    {
        self::displayTitle('cli');
        print_r($mValue);
        echo PHP_EOL;
    }
}
