<?php
/**
 * Created by PhpStorm.
 * User: ufukyaranli
 * Date: 9.11.2018
 * Time: 14:36
 */

namespace Yaranliu\Gadget\Exceptions;

use Exception;

class RelationNotExistingException extends Exception
{
    public function report()
    {

    }

    public function render($request)
    {
        return response()->json(['error' => '422', 'message' => 'Relation does not exist'], 422);
    }

}