<?php
/**
 * Created by PhpStorm.
 * User: Ufuk
 * Date: 01.03.2017
 * Time: 07:56
 */

namespace Yaranliu\Gadget\Contracts;


use Illuminate\Http\Request;

interface GadgetContract
{
    public function emptyArray(array $arrayToClean, $string = true, $array = true);

    public function setBit($byte, $bit);

    public function resetBit($byte, $bit);

    public function checkBit($byte, $bit);

    public function cArray($input);

    public function removeFromArray($item, array $array);

    public function tr_strtolower($text);

    public function tr_strtoupper($text);

    public function lowercase($text);

    public function uppercase($text);

    public function isTrue($param);

    public function dotToArray($item, $array = array());

    public function inputOrDefault(Request $request, $key, $default);

    public function keyAsArray(Request $request, $key, array $allItems = array(), array $defaultItems = array());

    public function with(Request $request, array $allRelations = array(), array $defaultRelations = array());

    public function querySorted($query, $definition, $dir = 'asc', $sortable = [], $strict = false);

    public function buildFilterItem($filter);

    public function getFilters($filterString);

    public function searchFilterAndSort(Request $request, $query, $searchable, $sortable = []);

}