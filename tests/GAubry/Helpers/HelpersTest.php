<?php

namespace GAubry\Helpers\Tests;

use \GAubry\Helpers\Helpers;

/**
 * @category TwengaDeploy
 * @package Tests
 * @author Geoffroy AUBRY <geoffroy.aubry@twenga.com>
 */
class HelpersTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers \GAubry\Helpers\Helpers::arrayMergeRecursiveDistinct
     * @dataProvider dataProvider_testArrayMergeRecursiveDistinct
     *
     * @param array $aArray1
     * @param array $aArray2
     * @param array $aResult
     */
    public function testArrayMergeRecursiveDistinct (array $aArray1, array $aArray2, array $aExpected)
    {
        $this->assertEquals($aExpected, Helpers::arrayMergeRecursiveDistinct($aArray1, $aArray2));
    }

    /**
     * Data provider pour testArrayMergeRecursiveDistinct()
     */
    public function dataProvider_testArrayMergeRecursiveDistinct ()
    {
        $aArray1 = array('a' => 'b', 'c' => array('d' => 'e'));
        return array(
            array(array(), array(), array()),
            array($aArray1, array(), $aArray1),
            array(array(), $aArray1, $aArray1),

            array(array(1, 2), array(3), array(3)),
            array(array(3), array(1, 2), array(1, 2)),

            array(array('a', 'b'), array('c'), array('c')),
            array(array('c'), array('a', 'b'), array('a', 'b')),

            array(array(3 => 'a', 'b' => 'c'), array(3 => null), array(3 => null, 'b' => 'c')),
            array(array(3 => null), array(3 => 'a', 'b' => 'c'), array(3 => 'a', 'b' => 'c')),

            array(array('a' => 'b'), array('a' => array(1, 2)), array('a' => array(1, 2))),
            array(array('a' => array(1, 2)), array('a' => 'b'), array('a' => 'b')),
            array(array('a' => array(1, 2)), array('a' => array(3)), array('a' => array(3))),

            array($aArray1, array('a' => 'x'), array('a' => 'x', 'c' => array('d' => 'e'))),
            array(array('a' => 'x'), $aArray1, array('a' => 'b', 'c' => array('d' => 'e'))),

            array(
                $aArray1,
                array('c' => array('d' => 'x'), 'y' => 'z'),
                array('a' => 'b', 'c' => array('d' => 'x'), 'y' => 'z')
            ),
            array(
                array('c' => array('d' => 'x'), 'y' => 'z'),
                $aArray1,
                array('a' => 'b', 'c' => array('d' => 'e'), 'y' => 'z')
            ),
        );
    }

    /**
     * @covers \GAubry\Helpers\Helpers::isAssociativeArray
     * @dataProvider dataProvider_testIsAssociativeArray
     *
     * @param array $aArray
     * @param bool $bResult
     */
    public function testIsAssociativeArray ($aArray, $bExpected)
    {
        $this->assertEquals($bExpected, Helpers::isAssociativeArray($aArray));
    }

    /**
     * Data provider pour testIsAssociativeArray()
     */
    public function dataProvider_testIsAssociativeArray ()
    {
        return array(
            array(array(), false),
            array(array('a'), false),
            array(array('a' => 1), true),
            array(array(1, 2), false),
            array(array(1, 'a' => 2, 3), true),
        );
    }

    /**
     * @covers \GAubry\Helpers\Helpers::stripBashColors
     * @dataProvider dataProvider_testStripBashColors
     *
     * @param string $sSource
     * @param string $sExpected
     */
    public function testStripBashColors ($sSource, $sExpected)
    {
        $this->assertEquals($sExpected, Helpers::stripBashColors($sSource));
    }

    /**
     * Data provider pour testStripBashColors()
     */
    public function dataProvider_testStripBashColors ()
    {
        return array(
            array('', ''),
            array('a', 'a'),
            array("\033[0m", ''),
            array("\033[1;34m", ''),
            array("\033[5;32;47m", ''),
            array("\033[1;34mxyz", 'xyz'),
            array("xyz\033[1;34m", 'xyz'),
            array("x\033[1;34my\033[0mz", 'xyz'),
            array("x\x1B[1;34my\x1B[0mz", 'xyz'),
        );
    }

    /**
     * @covers \GAubry\Helpers\Helpers::flattenArray
     * @dataProvider dataProvider_testFlattenArray
     *
     * @param array $aSource
     * @param array $aExpected
     */
    public function testFlattenArray (array $aSource, array $aExpected)
    {
        $this->assertEquals($aExpected, Helpers::flattenArray($aSource));
    }

    /**
     * Data provider pour testFlattenArray()
     */
    public function dataProvider_testFlattenArray ()
    {
        return array(
            array(array(), array()),
            array(array(1), array(1)),
            array(array('a' => 'b'), array('b')),
            array(array(1, 'a' => 'b'), array(1, 'b')),
            array(array(array('a' => 'b')), array('b')),
            array(array(1, array('a' => array('b', 2), 'c')), array(1, 'b', 2, 'c')),
        );
    }

    /**
     * @covers \GAubry\Helpers\Helpers::intToMultiple
     * @dataProvider dataProvider_testIntToMultiple
     *
     * @param int $iValue
     * @param array $aExpected
     */
    public function testIntToMultiple ($iValue, $bBinaryPrefix, array $aExpected)
    {
        $this->assertEquals($aExpected, Helpers::intToMultiple($iValue, $bBinaryPrefix));
    }

    /**
     * Data provider pour testIntToMultiple()
     */
    public function dataProvider_testIntToMultiple ()
    {
        return array(
            array(0, false, array(0, '')),
            array(10, false, array(10, '')),
            array(1024, false, array(1.024, 'k')),
            array(17825792, false, array(17.825792, 'M')),
            array(1073741824, false, array(1.073741824, 'G')),

            array(0, true, array(0, '')),
            array(10, true, array(10, '')),
            array(1024, true, array(1, 'Ki')),
            array(17825792, true, array(17, 'Mi')),
            array(1073741824, true, array(1, 'Gi')),
        );
    }

    /**
     * @covers \GAubry\Helpers\Helpers::round
     * @dataProvider dataProvider_testRound
     *
     * @param float $fValue
     * @param int $iPrecision
     * @param string $sExpected
     */
    public function testRound ($fValue, $iPrecision, $sExpected)
    {
        $this->assertEquals($sExpected, Helpers::round($fValue, $iPrecision));
    }

    /**
     * Data provider pour testRound()
     */
    public function dataProvider_testRound ()
    {
        return array(
            array(0, 0, '0'),
            array(1, 3, '1.000'),
            array(156.789, 0, '157'),
            array(156.789, 2, '156.79'),
            array(156.789, 4, '156.7890'),
            array(156.789, -1, '160'),
            array(156.789, -2, '200'),
            array(156.789, -3, '0'),
        );
    }

    /**
     * @covers \GAubry\Helpers\Helpers::ucwordWithDelimiters
     * @dataProvider dataProvider_testUcwordWithDelimiters
     *
     * @param string $sString
     * @param array $aDelimiters
     * @param string $sExpected
     */
    public function testUcwordWithDelimiters ($sString, array $aDelimiters, $sExpected)
    {
        $this->assertEquals($sExpected, Helpers::ucwordWithDelimiters($sString, $aDelimiters));
    }

    /**
     * Data provider pour testRound()
     */
    public function dataProvider_testUcwordWithDelimiters ()
    {
        return array(
            array('hello world', array(), 'Hello World'),
            array('HELLO world', array(), 'HELLO World'),
            array('hel-lo world', array(), 'Hel-lo World'),
            array('hel-lo world', array('-'), 'Hel-Lo World'),
            array("hel-lo wo'rld", array('-', "'"), "Hel-Lo Wo'Rld"),
        );
    }
}
