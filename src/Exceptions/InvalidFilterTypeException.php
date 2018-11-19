<?php
/**
 * Created by PhpStorm.
 * User: ufukyaranli
 * Date: 10.11.2018
 * Time: 21:02
 */

namespace Yaranliu\Gadget\Exceptions;

use Exception;

class InvalidFilterTypeException extends Exception
{
    public function report()
    {

    }

    public function render($request)
    {
        return response()->json(['error' => '500', 'message' => 'Invalid filter type in definition'], 500);
    }
}