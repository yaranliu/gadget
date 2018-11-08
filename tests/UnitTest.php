<?php
/**
 * Created by PhpStorm.
 * User: ufukyaranli
 * Date: 7.11.2018
 * Time: 12:25
 */

namespace Yaranliu\Gadget\Tests;

use Yaranliu\Gadget\Services\Gadget;

class UnitTest extends TestCase
{
    protected $gadget;

    public function setUp()
    {
        $this->gadget = new Gadget();
    }

    public function setBitProvider()
    {
        return array(
            array(162, 2, 166),
            array(178, 6, 242),
            array(36, 2, 36)
        );
    }

    /**
     * @param $a
     * @param $b
     * @param $c
     *
     * @dataProvider setBitProvider
     */
    public function testSetBit($a, $b, $c)
    {
        $this->assertEquals($this->gadget->setBit($a, $b), $c);
    }

    public function resetBitProvider()
    {
        return array(
            array(70, 2, 66),
            array(209, 4, 193),
            array(192, 5, 192)
        );
    }

    /**
     * @param $a
     * @param $b
     * @param $c
     *
     * @dataProvider resetBitProvider
     */
    public function testResetBit($a, $b, $c)
    {
        $this->assertEquals($this->gadget->resetBit($a, $b), $c);
    }

    public function checkBitProvider()
    {
        return array(
            array(193, 0, true),
            array(87, 6, true),
            array(87, 5, false),
        );
    }

    /**
     * @param $a
     * @param $b
     * @param $c
     *
     * @dataProvider checkBitProvider
     */
    public function testCheckBit($a, $b, $c)
    {
        $this->assertEquals($this->gadget->checkBit($a, $b), $c);
    }

    public function cArrayProvider()
    {
        return array(
            array(null, array()),
            array('test', array('test')),
            array(array(5, 7, 'aa'), array(5, 7, 'aa')),
            array('test|2|demo', array('test', '2', 'demo'))
        );
    }

    /**
     * @param $a
     * @param $b
     *
     * @dataProvider cArrayProvider
     */
    public function testCArray($a, $b)
    {
        $this->assertEquals($this->gadget->cArray($a), $b);
    }

    public function lowercaseProvider()
    {
        return array(
            array('ĞÜŞİÖÇ', 'ğüşiöç', 'tr'),
            array('ĞÜŞIÖÇ', 'ğüşıöç', 'tr'),
            array('ĞÜŞİÖÇ', 'ğüşiöç', 'en'),
            array('GUSIOC', 'gusioc', 'en'),
        );
    }

    /**
     * @param $a
     * @param $b
     * @param $c
     *
     * @dataProvider lowercaseProvider
     */
    public function testLowercase($a, $b, $c)
    {
        $this->assertEquals($this->gadget->lowercase($a, $c), $b);
    }

    public function uppercaseProvider()
    {
        return array(
            array('ğüşiöç', 'ĞÜŞİÖÇ', 'tr'),
            array('ğüşıöç', 'ĞÜŞIÖÇ', 'tr'),
            array('ğüşiöç', 'ĞÜŞIÖÇ', 'en'),
            array('gusioc', 'GUSIOC', 'en'),
        );
    }

    /**
     * @param $a
     * @param $b
     * @param $c
     *
     * @dataProvider uppercaseProvider
     */
    public function testUppercase($a, $b, $c)
    {
        $this->assertEquals($this->gadget->uppercase($a, $c), $b);
    }

    public function isTrueAsTrueProvider()
    {
        return array(
            array('Evet', array(), true, 'tr'),
            array('1', array(), true, 'tr'),
            array(1, array(), true, 'tr'),
            array('doĞru', array(), true, 'tr'),
            array('oN', array(), true, 'tr'),
            array('true', array(), true, 'tr'),
            array('correct', array('correct', 'açık'), true, 'tr'),
            array('AÇIK', array('correct', 'açık'), true, 'tr'),
            array('true', array('ok', 'tamam', 'always'), true, 'tr'),
            array(true, array(), true, 'tr'),
            array('doğru', array(), true, 'en'),
            array('true', array(), true, 'en'),
            array('1', array(), true, 'en'),
        );
    }

    /**
     * @param $a
     * @param $b
     * @param $c
     * @param $d
     *
     * @dataProvider isTrueAsTrueProvider
     */
    public function testIsTrueAsTrue($a, $b, $c, $d)
    {
        $this->assertTrue($this->gadget->isTrue($a, $b, $c, $d));
    }

    public function isTrueAsFalseProvider()
    {
        return array(
            array(null, array(), true, 'tr'),
            array('hayır', array(), true, 'tr'),
            array('0', array(), true, 'tr'),
            array(0, array(), true, 'tr'),
            array('YanlIş', array(), true, 'tr'),
            array('off', array(), true, 'tr'),
            array('false', array(), true, 'tr'),
            array(false, array(), true, 'tr'),
            array('KAPALI', array('correct', 'açık'), true, 'tr'),
            array('değil', array('correct', 'açık'), true, 'tr'),
            array('hatalı', array('ok', 'tamam', 'always'), true, 'tr'),
            array('false', array(), true, 'tr'),
            array('YANLIŞ', array(), true, 'en'),
            array('@', array(), true, 'en'),
            array(500, array(), true, 'en'),
        );
    }

    /**
     * @param $a
     * @param $b
     * @param $c
     * @param $d
     *
     * @dataProvider isTrueAsFalseProvider
     */
    public function testIsTrueAsFalse($a, $b, $c, $d)
    {
        $this->assertFalse($this->gadget->isTrue($a, $b, $c, $d));
    }
}