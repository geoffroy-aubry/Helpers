<?php

namespace GAubry\Tools\Tests;

use \GAubry\Tools\Tools;

/**
 * @category TwengaDeploy
 * @package Tests
 * @author Geoffroy AUBRY <geoffroy.aubry@twenga.com>
 */
class ToolsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers \GAubry\Tools\Tools::getFileSizeUnit
     * @dataProvider dataProvider_testGetFileSizeUnit
     * @param int $iFileSize taille en octets à changer d'unité
     * @param array $aExpected tableau (int, string) comprenant d'une part le nombre d'octets contenus dans la plus grande
     * unité inférieure à la taille spécifiée, et d'autre part le nom de cette unité.
     */
    public function testGetFileSizeUnit ($iFileSize, $aExpected)
    {
        $aResult = Tools::getFileSizeUnit($iFileSize);
        $this->assertEquals($aExpected, $aResult);
    }

    /**
     * Data provider pour testGetFileSizeUnit()
     */
    public static function dataProvider_testGetFileSizeUnit ()
    {
        return array(
            array(0, array(1, 'o')),
            array(100, array(1, 'o')),
            array(2000, array(1024, 'Kio')),
            array(2000000, array(1024*1024, 'Mio')),
        );
    }

    /**
     * @covers \GAubry\Tools\Tools::convertFileSize2String
     * @dataProvider dataProvider_testConvertFileSize2String
     * @param int $iSize taille à convertir
     * @param int $iRefSize référentiel de conversion, si différent de 0
     * @param array $aExpected un couple comprenant d'une part la taille spécifiée arrondie,
     * et d'autre part l'unité dans laquelle la taille a été arrondie.
     */
    public function testConvertFileSize2String ($iSize, $iRefSize, $aExpected)
    {
        $aResult = Tools::convertFileSize2String($iSize, $iRefSize);
        $this->assertEquals($aExpected, $aResult);
    }

    /**
     * Data provider pour testConvertFileSize2String()
     */
    public static function dataProvider_testConvertFileSize2String ()
    {
        return array(
            array(0, 0, array('0', 'o')),
            array(100, 0, array('100', 'o')),
            array(100, 2000000, array('<1', 'Mio')),
            array(2000, 0, array('2', 'Kio')),
            array(2000000, 0, array('2', 'Mio')),
        );
    }

    /**
     * @covers \GAubry\Tools\Tools::arrayMergeRecursiveDistinct
     * @dataProvider dataProvider_testArrayMergeRecursiveDistinct
     *
     * @param array $aArray1
     * @param array $aArray2
     * @param array $aResult
     */
    public function testArrayMergeRecursiveDistinct (array $aArray1, array $aArray2, array $aResult)
    {
        $this->assertEquals(Tools::arrayMergeRecursiveDistinct($aArray1, $aArray2), $aResult);
    }

    /**
     * Data provider pour testArrayMergeRecursiveDistinct()
     */
    public static function dataProvider_testArrayMergeRecursiveDistinct ()
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
     * @covers \GAubry\Tools\Tools::isAssociativeArray
     * @dataProvider dataProvider_testIsAssociativeArray
     *
     * @param unknown $aArray
     * @param unknown $bResult
     */
    public function testIsAssociativeArray ($aArray, $bResult)
    {
        $this->assertEquals(Tools::isAssociativeArray($aArray),$bResult);
    }

    /**
     * Data provider pour testIsAssociativeArray()
     */
    public static function dataProvider_testIsAssociativeArray ()
    {
        return array(
            array(array(), false),
            array(array('a'), false),
            array(array('a' => 1), true),
            array(array(1, 2), false),
            array(array(1, 'a' => 2, 3), true),
        );
    }
}
