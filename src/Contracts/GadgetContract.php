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
    public function configurationDefaults();

    public function emptyArray(array $arrayToClean, $string = true, $array = true);

    public function setBit($word, $bit);

    public function resetBit($word, $bit);

    public function checkBit($word, $bit);

    public function cArray($input);

    public function removeFromArray($item, array $array);

    public function tr_strtolower($text);

    public function tr_strtoupper($text);

    public function lowercase($text);

    public function uppercase($text);

    public function isTrue($param);

    public function isFalse($param);

    public function dotToArray($item, $array = array());

    public function inputOrDefault($key, $default);

    public function keyAsArray($key, array $allItems = array(), array $defaultItems = array());

    public function with(array $allRelations = array(), array $defaultRelations = array());

    public function querySorted($query, $definition, $dir = 'asc', $sortable = [], $strict = false);

    public function getFilters($filterString);

    public function searchFilterAndSort($query, array $searchable, array $sortable = []);

    public function autoReference($table, $forKey = "reference", $userDomainId = null, $domainKey = "domain_id", $padLength = 10, $padString = "0");

    public function addFillables(array $validate, $class, array $except = []);

    public function calc_per_page();

    public function getPaginated($query, $perPage);

}