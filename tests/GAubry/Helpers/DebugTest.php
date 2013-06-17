<?php

namespace GAubry\Helpers\Tests;

use GAubry\Helpers\Debug;



class DebugTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp ()
    {
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
    }

    /**
     * @dataProvider dataProviderTestVarDumpWithVariable
     */
    public function testVarDumpWithVariable ($mValue)
    {
        ob_start();
        var_dump($mValue);
        $sExpected = ob_get_contents();
        ob_end_clean();

        $sExpected = sprintf(Debug::$sDisplayPatterns['cli'], __FUNCTION__ . '()', __FILE__, __LINE__ + 4, '$mValue')
                   . $sExpected . PHP_EOL;

        ob_start();
        Debug::varDump($mValue);
        $sResult = ob_get_contents();
        ob_end_clean();

        $sResult = str_replace("\033", '\033', $sResult);
        $sExpected = str_replace("\033", '\033', $sExpected);
        $this->assertEquals($sExpected, $sResult);
    }

    /**
     * @dataProvider dataProviderTestVarDumpWithVariable
     */
    public function testPrintrWithVariable ($mValue)
    {
        $sExpected = sprintf(Debug::$sDisplayPatterns['cli'], __FUNCTION__ . '()', __FILE__, __LINE__ + 4, '$mValue')
                   . print_r($mValue, true) . PHP_EOL;

        ob_start();
        Debug::printr($mValue);
        $sResult = ob_get_contents();
        ob_end_clean();

        $sResult = str_replace("\033", '\033', $sResult);
        $sExpected = str_replace("\033", '\033', $sExpected);
        $this->assertEquals($sExpected, $sResult);
    }

    /**
     * Data provider pour testVarDumpWithVariable()
     */
    public function dataProviderTestVarDumpWithVariable ()
    {
        return array(
            array('Hello!'),
            array((int)5),
            array(array('message' => 'Hello!')),
            array(new \stdClass()),
        );
    }

    public function testVarDumpWithEval ()
    {
        $sValue = 'Hello!';
        ob_start();
        var_dump($sValue);
        $sExpected = ob_get_contents();
        ob_end_clean();

        $sExpected = sprintf(Debug::$sDisplayPatterns['cli'], 'eval()', __FILE__, __LINE__ + 4, "'$sValue'")
                   . $sExpected . PHP_EOL;

        ob_start();
        eval("\\GAubry\\Helpers\\Debug::varDump('Hello!');");
        $sResult = ob_get_contents();
        ob_end_clean();

        $sResult = str_replace("\033", '\033', $sResult);
        $sExpected = str_replace("\033", '\033', $sExpected);
        $this->assertEquals($sExpected, $sResult);
    }

    public function testVarDumpWithValueAndParentheses ()
    {
        $sValue = 'Hello!()';
        ob_start();
        var_dump($sValue);
        $sExpected = ob_get_contents();
        ob_end_clean();

        $sExpected = sprintf(Debug::$sDisplayPatterns['cli'], __FUNCTION__ . '()', __FILE__, __LINE__ + 4, "'$sValue'")
                   . $sExpected . PHP_EOL;

        ob_start();
        Debug::varDump('Hello!()');
        $sResult = ob_get_contents();
        ob_end_clean();

        $sResult = str_replace("\033", '\033', $sResult);
        $sExpected = str_replace("\033", '\033', $sExpected);
        $this->assertEquals($sExpected, $sResult);
    }
}
