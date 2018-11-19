<?php
/**
 * Created by PhpStorm.
 * User: ufukyaranli
 * Date: 7.11.2018
 * Time: 12:25
 */

namespace Yaranliu\Gadget\Tests;

use Mockery\Mock;
use Yaranliu\Gadget\Exceptions\RelationNotExistingException;
use Yaranliu\Gadget\Facades\Gadget;

class UnitTest extends TestCase
{

//    public function setUp()
//    {
//        parent::setUp();
//    }

    public function emptyArrayProvider()
    {
        return array(
            array(array("", 5, 'a'), array(5, 'a')),
            array(array("", 5, 'a', [], false), array(5, 'a', false)),
        );
    }

    /**
     * @param $a
     * @param $e
     *
     * @dataProvider emptyArrayProvider
     */
    public function testEmptyArray($a, $e)
    {
        $this->assertEquals(Gadget::emptyArray($a), $e);
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
        $this->assertEquals(Gadget::setBit($a, $b), $c);
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
        $this->assertEquals(Gadget::resetBit($a, $b), $c);
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
        $this->assertEquals(Gadget::checkBit($a, $b), $c);
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
//        $this->assertEquals(Gadget::cArray($a), $b);
        $this->assertEquals(Gadget::cArray($a), $b);
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
        $this->assertEquals(Gadget::lowercase($a, $c), $b);
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
        $this->assertEquals(Gadget::uppercase($a, $c), $b);
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
        $this->assertTrue(Gadget::isTrue($a, $b, $c, $d));
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
        $this->assertFalse(Gadget::isTrue($a, $b, $c, $d));
    }

    public function dotToArrayProvider()
    {
        return array(
            array('color.red', array('shape' => ['box'], 'size' => ['large']), array('shape' => ['box'], 'size' => ['large'], 'color' => ['red'])),
            array('color.red', array(), array('color' => ['red'])),
            array('color.red', null, array('color' => ['red'])),
        );
    }

    /**
     * @param $a
     * @param $b
     * @param $c
     * @dataProvider dotToArrayProvider
     */
    public function testDotToArray($a, $b, $c)
    {
        $this->assertEquals(Gadget::dotToArray($a, $b), $c);
    }


    public function inputOrDefaultProvider()
    {
        return [
          [ '/test?key=value', 'key' => 'key', 'default' => 'default', 'expected' => 'value'],
          [ '/test?key=', 'key' => 'key', 'default' => 'default', 'expected' => null],
          [ '/test?key=red', 'key' => 'key', 'default' => 'default', 'expected' => 'red'],
        ];
    }


    /**
     * @param $u
     * @param $k
     * @param $d
     * @param $e
     *
     * @dataProvider inputOrDefaultProvider
     */
    public function testInputOrDefault($u, $k, $d, $e)
    {
        $this->get($u);

        $this->assertEquals(Gadget::inputOrDefault($k, $d), $e);
    }

    public function keyAsArrayProvider()
    {
        return [
//            [ url, key, all, default, expected],
            [ '/test?key=2', 'key', ['1', '2', '3'], ['3'], ['2']],
            [ '/test?key=all', 'key', ['1', '2', '3'], ['3'], ['1', '2', '3']],
            [ '/test?key=1|2|3', 'key', ['1', '2', '3'], ['3'], ['1', '2', '3']],
            [ '/test?key=1|2|3|4', 'key', ['1', '2', '3'], ['3'], false],
            [ '/test?key=2', 'key', ['1', 2, '3'], [2], [2]],
            [ '/test?key=45.33', 'key', [45, 45.33], [45], [45.33]],
            [ '/test?key=1', 'key', ['1', '0', true, false], [false], [true]],
            [ '/test?key=a|b', 'key', ['a', 'b', true, false], [false], ['a', 'b']],
        ];

    }

    public function keyAsArrayExceptionProvider()
    {
        return [
            [ '/test?key=2',       'key',  1,                 ['3']],
            [ '/test?key=all',     'key',  ['1', '2', '3'],   7],
            [ '/test?key=2',       'key',  ['1', '2', '3'],   null],
            [ '/test?key=2',       'key',  2,                 '3'],
            [ '/test?key=2',       'key',  [2],               3],
            [ '/test?key=2',       'key',  null,              ['3', 4]],
        ];
    }

    public function keyAsArrayReturnDefaultProvider()
    {
        return [
            [[1], ['3'], ['3']],
            [['1', '2', true], [[1,2]], [[1,2]]],
            [['1', '2', '3'], [false], [false]],
            [[[2, 5, 'test'], 'test', '3'], [1, true], [1, true]],

        ];
    }

    /**
     * @param $u
     * @param $k
     * @param $a
     * @param $d
     * @param $e
     *
     * @dataProvider keyAsArrayProvider
     */
    public function testKeyAsArray($u, $k, $a, $d, $e)
    {
        $this->get($u);

        $this->assertEquals(Gadget::keyAsArray($k, $a, $d), $e);
    }

    /**
     * @param $u
     * @param $k
     * @param $a
     * @param $d
     *
     * @dataProvider keyAsArrayExceptionProvider
     */
    public function testKeyAsArrayException($u, $k, $a, $d)
    {

        $this->get($u);

        $this->expectException(\TypeError::class);

        Gadget::keyAsArray($k, $a, $d);

    }

    /**
     * @param $a
     * @param $d
     * @param $e
     *
     * @dataProvider keyAsArrayReturnDefaultProvider
     */
    public function testKeyAsArrayReturnDefault($a, $d, $e)
    {
        $this->get('/test');

        $this->assertEquals(Gadget::keyAsArray('key', $a, $d), $e);
    }

    public function withProvider()
    {
        return [
            [ '/test?with=all', ['likes', 'posts', 'followers', 'following'], ['likes'], ['likes', 'posts', 'followers', 'following']],
            [ '/test?with=likes|following', ['likes', 'posts', 'followers', 'following'], ['likes'], ['likes', 'following']],
            [ '/test?with=', ['likes', 'posts', 'followers', 'following'], ['likes'], ['likes']],
            [ '/test?with=posts', ['likes', 'posts', 'followers', 'following'], ['following'], ['posts']],
            [ '/test?relations=posts', ['likes', 'posts', 'followers', 'following'], ['following'], ['following']],
        ];
    }

    /**
     * @param $u
     * @param $a
     * @param $d
     * @param $e
     *
     * @dataProvider withProvider
     */
    public function testWithProvider($u, $a, $d, $e)
    {
        $this->get($u);

        $this->assertEquals(Gadget::with($a, $d), $e);
    }

    public function withExceptionProvider()
    {
        return [
            [ '/test?with=likes|following', ['likes', 'posts', 'followers'], ['likes']],
            [ '/test?with=posts', ['likes', 'followers', 'following'], ['following']],
        ];

    }

    /**
     * @param $u
     * @param $a
     * @param $d
     *
     * @dataProvider withExceptionProvider
     */
    public function testWithException($u, $a, $d)
    {

        $this->expectException(RelationNotExistingException::class);

        $this->get($u);

        Gadget::with($a, $d);

    }

    public function buildFilterItemProvider()
    {
        return array(
            array('age.gt.[35]',[
              'field' => 'age',
              'operator' => 'gt',
              'values' => [35]]),
            array('color.in.[red~blue~magenta]',[
              'field' => 'color',
              'operator' => 'in',
              'values' => ['red', 'blue', 'magenta']]),
            array('color.in.[red~blue~magenta[', false),
            array('color.in.red~blue~magenta]', false),
            array('color.in.][red~blue~magenta', false),
            array('color.in][red~blue~magenta', false),
            array('color.in.][red~blue~magenta', false),
            array('colorin.[red~blue~magenta]', false),
            array('.lte.[red~blue~magenta]', false),
            array('age.gt[red~blue~magenta]', false),
            array('color.in.[red|blue|magenta]', [
                'field' => 'color',
                'operator' => 'in',
                'values' => ['red|blue|magenta']]),
            array('color.in.[red~blue~magenta~]', [
                'field' => 'color',
                'operator' => 'in',
                'values' => ['red', 'blue', 'magenta']]),
            array('color.in.[~~~red~blue~magenta~]', [
                'field' => 'color',
                'operator' => 'in',
                'values' => ['red', 'blue', 'magenta']]),
            array('color.in.values->>[~~~red~blue~magenta~]', [
                'field' => 'color',
                'operator' => 'in',
                'values' => ['red', 'blue', 'magenta']]),
            array('color.in.[]', null),
            array('color.in.[~~]', null),
        );
    }

    /**
     * @param $f
     * @param $e
     *
     * @dataProvider buildFilterItemProvider
     */
    public function testBuildFilterItem($f, $e)
    {
        $this->assertEquals(Gadget::buildFilterItem($f), $e);
    }

    public function getFiltersProvider()
    {
        return array(
            array('age.gt.[35]|likes.lte.[30]|color.in.[red~yellow]||',
                [
                    [
                        'field' => 'age',
                        'operator' => 'gt',
                        'values' => [35]
                        ],
                    [
                        'field' => 'likes',
                        'operator' => 'lte',
                        'values' => [30]
                        ],
                    [
                        'field' => 'color',
                        'operator' => 'in',
                        'values' => ['red', 'yellow']
                    ]]),
            array('age.gt..]35]|likes.lte.[30]|color.in.[red~yellow]||', false),
            array('age.gt.[35]|likes..[30]|color.in.[red~yellow]||', false),
            array('..gt..]35]|likes.lte.[30]|color.in.[red~yellow]||', false),
        );
    }

    /**
     * @param $s
     * @param $e
     *
     * @dataProvider getFiltersProvider
     */
    public function testGetFilters($s, $e)
    {
        $this->assertEquals(Gadget::getFilters($s), $e);
    }


}