<?php

namespace Yaranliu\Gadget\Services;

use Illuminate\Config\Repository;
use Yaranliu\Gadget\Contracts\GadgetContract;

class Gadget implements GadgetContract
{
    protected $config;
    protected $app;

    public function __construct()
    {
        $this->config = new Repository();
    }

    /**
     * Sets nth bit ($bit) of $byte to 1
     *
     * @param $byte
     * @param $bit
     * @return int
     */
    public function setBit($byte, $bit)
    {
        return ($byte | pow(2, $bit));
    }

    /**
     * Sets nth bit ($bit) of the $byte to 0
     *
     * @param $byte
     * @param $bit
     * @return int
     */
    public function resetBit($byte, $bit)
    {
        return ($byte & (255 - pow(2, $bit)));
    }

    /**
     * Returns nth bit ($bit) of the $byte as true (1) or false (0)
     *
     * @param $byte
     * @param $bit
     * @return bool
     */
    public function checkBit($byte, $bit)
    {
        return ($byte & pow(2, $bit)) == pow(2, $bit);
    }

    /**
     * If $input is an array, returns $input.
     * If $input is a string, returns array of exploding the string with the delimiter defined in configuration file
     *
     * @param $input
     * @return array
     */
    public function cArray($input)
    {
        if (is_null($input)) return array();
        if (is_array($input)) return $input;
        if (is_string($input)) return explode($this->config->get('gadget.delimiter.item', '|'), $input);
        else return array($input);
    }

    /**
     * Removes a $item from $array and returns the $array
     *
     * @param $item
     * @param array $array
     * @return array
     */
    public function removeFromArray($item, array $array)
    {
        $key = array_search($item, $array);
        if ($key !== false) unset($array[$key]);
        return $array;
    }

    /**
     * Converts $text to lowercase using Turkish character set and multi byte conversion
     *
     * @param $text
     * @return mixed|null|string|string[]
     */
    public function tr_strtolower($text)
    {
        return mb_strtolower(str_replace('I', 'ı', $text), 'UTF-8');
    }

    /**
     * Converts $text to uppercase using Turkish character set and multi byte conversion
     *
     * @param $text
     * @return mixed|null|string|string[]
     */
    public function tr_strtoupper($text)
    {
        return mb_strtoupper(str_replace('i', 'İ', $text), 'UTF-8');
    }

    /**
     * Converts $text to lowercase depending on $locale (default is 'tr')
     * tr_strtolower is used if $locale is 'tr';  otherwise, mb_strtolower is used
     *
     * @param $text
     * @param string $locale
     * @return mixed|null|string|string[]
     */
    public function lowercase($text, $locale = 'tr')
    {
        return ($locale === 'tr') ? $this->tr_strtolower($text) : mb_strtolower($text);
    }

    /**
     * Converts $text to uppercase depending on $locale (default is 'tr').
     * tr_strtoupper is used if $locale is 'tr';  otherwise, mb_strtoupper is used
     *
     * @param $text
     * @param string $locale
     * @return mixed|null|string|string[]
     */
    public function uppercase($text, $locale = 'tr')
    {
        return ($locale === 'tr') ? $this->tr_strtoupper($text) : mb_strtoupper($text);
    }

    /**
     * Interprets $input as boolean true or false.
     * Checks if $input is an item of the following:
     * '1', 'yes', 'true', 'on', 1, 'evet', 'doğru'
     *
     * If provided, the items of $base will be interpreted as True.
     * Providing $default as false will use only $base for interpretation
     *
     * $locale is used for case-insensitive check and defaults to 'tr' (Turkish)
     *
     * @param $param
     * @param array $base
     * @param bool $default
     * @param string $locale
     * @return bool
     */
    public function isTrue($param, $base = array(), $default = true, $locale = 'tr')
    {
        if (is_null($param)) return false;
        if (is_bool($param)) return $param;
        $array = ($default) ? array_merge(['1', 'yes', 'true', 'on', 1, 'evet', 'doğru'], $base) : $base;
        return in_array($this->lowercase($param, $locale), $array);
    }

    /**
     * * Converts dotted string $item to an associative array or appends to the provided $array
     *
     * $array = dotToArray('tags.name');
     * $array = dotToArray('tags.color', $array);
     * $array = dotToArray('tags.color', $array);       // Duplicate
     * $array = dotToArray('pictures.name', $array);
     * $array = dotToArray('pictures.title', $array);
     *
     * $array = ['tags' => ['name', 'color'],  'picture' => ['name', 'title']]
     *
     * @param $item
     * @param $array
     * @return array
     */
    public function dotToArray($item, $array = array())
    {
        if (is_null($array)) $array = array();
        $exploded = explode('.', $item);
        if (is_null($exploded) || empty($exploded) || (count($exploded) !== 2)) return $array;
        else{
            if (array_key_exists($exploded[0], $array))
            {
                if (!in_array($exploded[1], $array[$exploded[0]]))
                    $array[$exploded[0]][] = $exploded[1];
            }
            else $array[$exploded[0]] = array($exploded[1]);
            return $array;
        }
    }
}