<?php
/**
 * Created by PhpStorm.
 * User: ufukyaranli
 * Date: 14.11.2018
 * Time: 15:13
 */

namespace Yaranliu\Gadget\Exceptions;


class ResourceOwnerArrayNotValidException extends \Exception
{

    public function report()
    {

    }

    public function render($request)
    {
        return response()->json(['error' => '500', 'message' => 'Resource owner array definition ($resourceOwner) is not valid'], 500);
    }

}