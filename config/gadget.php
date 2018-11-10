<?php
/**
 * Created by PhpStorm.
 * User: Ufuk
 * Date: 01.03.2017
 * Time: 08:41
 */

return [

    'config' => 'api',
    'per_page' => 20,
    'lookup_limit' =>  10,

    'word' => [
        'filter' => 'filter',
        'search' =>  'search',
        'order_by' => 'sort_by',
        'descending' => 'descending',
        'all' =>  'all',
        'per_page' =>'per_page',
    ],

    'sign' => [
        'delimiter' => [
            'first' => '|',
            'second' => '~',
        ],
        'list' => [
            'start' => '[',
            'end' => ']',
        ],
        'sibling' => '.'
    ],

];