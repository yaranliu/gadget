<?php
/**
 * Created by PhpStorm.
 * User: Ufuk
 * Date: 01.03.2017
 * Time: 07:56
 */

namespace Yaranliu\Gadget\Contracts;


interface GadgetContract
{
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

}