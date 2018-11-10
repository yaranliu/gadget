<?php
/**
 * Created by PhpStorm.
 * User: ufukyaranli
 * Date: 7.11.2018
 * Time: 12:25
 */

namespace Yaranliu\Gadget\Tests;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Yaranliu\Gadget\Exceptions\RelationNotExistingException;
use Yaranliu\Gadget\Services\Gadget;

class UnitTest extends TestCase
{
    protected $gadget;

    public function setUp()
    {
        $this->gadget = new Gadget();
    }

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
        var_dump($this->gadget->emptyArray($a));
        $this->assertEquals($this->gadget->emptyArray($a), $e);
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
        $this->assertEquals($this->gadget->dotToArray($a, $b), $c);
    }


    public function inputOrDefaultProvider()
    {
        $test = [
          [ 'key' => 'value', 'default' => 'default', 'expected' => 'value'],
          [ 'key' => null, 'default' => 'default', 'expected' => null],
          [ 'key' => 'red', 'default' => 'default', 'expected' => 'red'],
        ];

        $provider = array();

        foreach ($test as $testItem) {

            $r = new Request();

            $r->replace(['key' => $testItem['key']]);

            $provider[] = [$r, 'key', $testItem['default'], $testItem['expected']];

        }
        $rEmpty = new Request();

        $provider[] = [$rEmpty, 'key', 'default', 'default'];

        return $provider;
    }


    /**
     * @param $r
     * @param $k
     * @param $d
     * @param $e
     *
     * @dataProvider inputOrDefaultProvider
     */
    public function testInputOrDefault($r, $k, $d, $e)
    {
        $this->assertEquals($this->gadget->inputOrDefault($r, $k, $d), $e);
    }

    public function keyAsArrayProvider()
    {
        $test = [
            [ 'key' => 'key', 'value' => '2', 'all' => ['1', '2', '3'], 'default' => ['3'], 'expected' => ['2']],
            [ 'key' => 'key', 'value' => 'all', 'all' => ['1', '2', '3'], 'default' => ['3'], 'expected' => ['1', '2', '3']],
            [ 'key' => 'key', 'value' => '1|2|3', 'all' => ['1', '2', '3'], 'default' => ['3'], 'expected' => ['1', '2', '3']],
            [ 'key' => 'key', 'value' => '1|2|3|4', 'all' => ['1', '2', '3'], 'default' => ['3'], 'expected' => false],
            [ 'key' => 'key', 'value' => 2, 'all' => ['1', 2, '3'], 'default' => [2], 'expected' => [2]],
            [ 'key' => 'key', 'value' => 45.33, 'all' => [45, 45.33], 'default' => [45], 'expected' => [45.33]],
            [ 'key' => 'key', 'value' => true, 'all' => ['1', '0', true, false], 'default' => [false], 'expected' => [true]],
            [ 'key' => 'key', 'value' => 'a|b', 'all' => ['a', 'b', true, false], 'default' => [false], 'expected' => ['a', 'b']],
        ];

        $provider = array();

        foreach ($test as $testItem) {

            $r = new Request();

            $r->replace([$testItem['key'] => $testItem['value']]);

            $provider[] = [$r, $testItem['key'], $testItem['all'], $testItem['default'], $testItem['expected']];

        }

        return $provider;
    }

    public function keyAsArrayExceptionProvider()
    {
        $test = [
            [ 'key' => 'key', 'value' => '2', 'all' => 1, 'default' => ['3']],
            [ 'key' => 'key', 'value' => 'all', 'all' => ['1', '2', '3'], 'default' => 7],
            [ 'key' => 'key', 'value' => '2', 'all' => ['1', '2', '3'], 'default' => null],
            [ 'key' => 'key', 'value' => '2', 'all' => 2, 'default' => '3'],
            [ 'key' => 'key', 'value' => '2', 'all' => [2], 'default' => 3],
            [ 'key' => 'key', 'value' => '2', 'all' => null, 'default' => ['3', 4]],
        ];

        $provider = array();

        foreach ($test as $testItem) {

            $r = new Request();

            $r->replace([$testItem['key'] => $testItem['value']]);

            $provider[] = [$r, $testItem['key'], $testItem['all'], $testItem['default']];

        }

        return $provider;
    }

    public function keyAsArrayReturnDefaultProvider()
    {
        $test = [
            ['all' => [1], 'default' => ['3'], 'expected' => ['3']],
            ['all' => ['1', '2', true], 'default' => [[1,2]], 'expected' => [[1,2]]],
            ['all' => ['1', '2', '3'], 'default' => [false], 'expected' => [false]],
            ['all' => [[2, 5, 'test'], 'test', '3'], 'default' => [1, true], 'expected' => [1, true]],

        ];

        $provider = array();

        foreach ($test as $testItem) {

            $r = new Request();

            $provider[] = [$r, $testItem['all'], $testItem['default'], $testItem['expected']];

        }

        return $provider;
    }

    /**
     * @param $r
     * @param $k
     * @param $a
     * @param $d
     * @param $e
     *
     * @dataProvider keyAsArrayProvider
     */
    public function testKeyAsArray($r, $k, $a, $d, $e)
    {
        $this->assertEquals($this->gadget->keyAsArray($r, $k, $a, $d), $e);
    }

    /**
     * @param $r
     * @param $k
     * @param $a
     * @param $d
     *
     * @dataProvider keyAsArrayExceptionProvider
     */
    public function testKeyAsArrayException($r, $k, $a, $d)
    {

        $this->expectException(\TypeError::class);

        $this->gadget->keyAsArray($r, $k, $a, $d);

    }

    /**
     * @param $r
     * @param $a
     * @param $d
     * @param $e
     *
     * @dataProvider keyAsArrayReturnDefaultProvider
     */
    public function testKeyAsArrayReturnDefault($r, $a, $d, $e)
    {
        $this->assertEquals($this->gadget->keyAsArray($r, 'key', $a, $d), $e);
    }

    public function withRelationsProvider()
    {
        $test = [
            [ 'key' => 'with', 'value' => 'all', 'all' => ['likes', 'posts', 'followers', 'following'], 'default' => ['likes'], 'expected' => ['likes', 'posts', 'followers', 'following']],
            [ 'key' => 'with', 'value' => 'likes|following', 'all' => ['likes', 'posts', 'followers', 'following'], 'default' => ['likes'], 'expected' => ['likes', 'following']],
            [ 'key' => 'with', 'value' => '', 'all' => ['likes', 'posts', 'followers', 'following'], 'default' => ['likes'], 'expected' => ['likes']],
            [ 'key' => 'with', 'value' => null, 'all' => ['likes', 'posts', 'followers', 'following'], 'default' => ['likes'], 'expected' => ['likes']],
            [ 'key' => 'with', 'value' => 'posts', 'all' => ['likes', 'posts', 'followers', 'following'], 'default' => ['following'], 'expected' => ['posts']],
        ];

        $provider = array();

        foreach ($test as $testItem) {

            $r = new Request();

            $r->replace([$testItem['key'] => $testItem['value']]);

            $provider[] = [$r, $testItem['key'], $testItem['all'], $testItem['default'], $testItem['expected']];

        }

        return $provider;
    }

    /**
     * @param $r
     * @param $k
     * @param $a
     * @param $d
     * @param $e
     *
     * @dataProvider withRelationsProvider
     */
    public function testWithRelationsProvider($r, $k, $a, $d, $e)
    {
        $this->assertEquals($this->gadget->withRelations($r, $k, $a, $d), $e);
    }

    public function withRelationsExceptionProvider()
    {
        $test = [
            [ 'key' => 'with', 'value' => 'likes|following', 'all' => ['likes', 'posts', 'followers'], 'default' => ['likes']],
            [ 'key' => 'with', 'value' => 'posts', 'all' => ['likes', 'followers', 'following'], 'default' => ['following']],
        ];

        $provider = array();

        foreach ($test as $testItem) {

            $r = new Request();

            $r->replace([$testItem['key'] => $testItem['value']]);

            $provider[] = [$r, $testItem['key'], $testItem['all'], $testItem['default']];

        }

        return $provider;
    }

    /**
     * @param $r
     * @param $k
     * @param $a
     * @param $d
     *
     * @dataProvider withRelationsExceptionProvider
     */
    public function testWithRelationsException($r, $k, $a, $d)
    {

        $this->expectException(RelationNotExistingException::class);

        $this->gadget->withRelations($r, $k, $a, $d);

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
        $this->assertEquals($this->gadget->buildFilterItem($f), $e);
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
        $this->assertEquals($this->gadget->getFilters($s), $e);
    }

}