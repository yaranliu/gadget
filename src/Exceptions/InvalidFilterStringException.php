<?php
/**
 * Created by PhpStorm.
 * User: ufukyaranli
 * Date: 10.11.2018
 * Time: 19:18
 */

namespace Yaranliu\Gadget\Exceptions;

use Exception;

class InvalidFilterStringException extends Exception
{
    public function report()
    {

    }

    public function render($request)
    {
        return response()->json(['error' => '422', 'message' => 'Invalid filter string'], 422);
    }
}