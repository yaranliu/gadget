<?php
/**
 * Created by PhpStorm.
 * User: ufukyaranli
 * Date: 2019-01-22
 * Time: 13:17
 */

namespace Yaranliu\Gadget\Classes;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Yaranliu\Gadget\Exceptions\RelationNotExistingException;
use Yaranliu\Gadget\Services\Gadget;

class ParsedRequest
{
    protected $request;

    public $with;

    /**
     * ParsedRequest constructor.
     * @param Request $request
     * @param array $allRelations
     * @param array $defaultRelations
     * @throws RelationNotExistingException
     */
    public function __construct(Request $request, array $allRelations = array(), array $defaultRelations = array())
    {
        $this->request = $request;

        $this->with = $this->parseWith($allRelations, $defaultRelations);
    }

    /**
     * @param array $allRelations
     * @param array $defaultRelations
     * @return array|bool|string|null
     * @throws RelationNotExistingException
     */
    private function parseWith(array $allRelations = array(), array $defaultRelations = array())
    {
        $gadget = new Gadget();

        $wWith = Config::get('gadget.word.with', 'with');
        if ($this->request->has($wWith)) {
            $array = $gadget->keyAsArray($wWith, $allRelations, $defaultRelations);

            if ($array === false) throw new RelationNotExistingException();
            else return $array;

        } else return $defaultRelations;

    }

}