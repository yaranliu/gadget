<?php

namespace Yaranliu\Gadget\Services;

use Illuminate\Config\Repository;
use Illuminate\Http\Request;
use function PHPSTORM_META\type;
use Yaranliu\Gadget\Contracts\GadgetContract;
use Yaranliu\Gadget\Exceptions\InvalidFilterStringException;
use Yaranliu\Gadget\Exceptions\RelationNotExistingException;
use Yaranliu\Gadget\Exceptions\UnknownFilterOperatorException;

class Gadget implements GadgetContract
{
    protected $config;
    protected $app;

    public function __construct()
    {
        $this->config = new Repository();
    }

    /**
     * Removes empty strings and/or arrays from a plain array
     *
     * @param array $arrayToClean
     * @param bool $string
     * @param bool $array
     * @return array
     */
    public function emptyArray(array $arrayToClean, $string = true, $array = true)
    {
        $a = [];
        foreach ($arrayToClean as $item) {
            $add = true;
            if ((gettype($item) === 'string') && $string) {
                if ($item === "") $add = false;
            } else if ((gettype($item) === 'array') && $array) {
                if (empty($item)) $add = false;
            }
            if ($add) $a[] = $item;
        }
        return $a;
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
        if (is_string($input)) return explode($this->config->get('gadget.sign.delimiter.first', '|'), $input);
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
     * Converts dotted string $item to an associative array or appends to the provided $array
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

    // Request Processors
    // ------------------

    /**
     * Returns the request parameter's value or the default value
     *
     * @param \Illuminate\Http\Request $request
     * @param $key
     * @param $default
     * @return array|null|string
     */
    public function inputOrDefault(Request $request, $key, $default)
    {
        if ( $request->has($key) ) {
            return ($request->input($key) === "") ? $default : $request->input($key);
        }
        else return $default;
    }

    /**
     * Explodes the request parameter ($key) into an array and returns this array.
     * If the $key does not exist on the request then the $defaultItems (array) is returned.
     * If $key is 'all', $allItems is returned.
     *
     * If there is an item in the request param array which is NOT an element of the $allItems array, FALSE is returned
     *
     * @param \Illuminate\Http\Request $request
     * @param $key
     * @param array $allItems
     * @param array $defaultItems
     * @return array|bool|null|string
     */
    public function keyAsArray(Request $request, $key, array $allItems = array(), array $defaultItems = array())
    {
        $items = $this->inputOrDefault($request, $key, array());

        if (!empty($items))
        {
            if (strtolower($items) === $this->config->get('gadget.word.all', 'all')) $items = $allItems;
            else {
                $items = explode($this->config->get('gadget.sign.delimiter.first', '|'), $items);
                if (!empty(array_diff($items, $allItems))) return false;
            }
        }

        return (empty($items) ? $defaultItems : $items);
    }

    /**
     * Utilizes keyAsArray function to obtain which relations to be loaded by the request.
     *
     * RelationNotExistingException is  thrown if there is an item in the request param array
     * which is NOT an element of the $allItems array,
     *
     * Use case:
     * if HTTP Request has a parameter e.g. 'with',  decides which relations are to be loaded by looking at the Model's
     * $relations (all) and $defaultRelations (if there is no 'with' parameter on the request)
     *
     * If client sends a HTTP request ?with=all all of the relations will be loaded.
     *
     * $with = Gadget::loadRelations($request, 'with', $this->allRelations, $this->defaultRelations);
     * $entities = Model::with($with)->all();
     *
     * Please note that 'allRelations' and 'defaultRelations' properties must be declared as array on the Model class
     *
     * @param \Illuminate\Http\Request $request
     * @param array $allRelations
     * @param array $defaultRelations
     * @return array|null|string
     * @throws RelationNotExistingException
     */
    public function with(Request $request, array $allRelations = array(), array $defaultRelations = array())
    {

        $wWith = $this->config->get('gadget.word.with', 'with');
        if ($request->has($wWith)) {
            $array = $this->keyAsArray($request, $wWith, $allRelations, $defaultRelations);

            if ($array === false) throw new RelationNotExistingException();
            else return $array;
        } else return $defaultRelations;

    }

    /**
     * Adds sorting to and returns provided $query.
     *
     * Example for $sortable argument:
     *
     * public static $sortable = [
     *          'full_name' => ['title', 'name'],
     *          'name'  => ['name']
     *          'job_title' => false,
     *      ];
     *
     * If there are more than one column to be sorted according to $definition argument, these columns must exist in the array
     * and will be appended to the orderBy clause.
     *
     * If you want to exclude $field from sorting anyway, provide false for $field definition in $sortable array.
     *
     * If $field is not in $sortable and $strict is false, $field will be treated a column name and $query will simply be
     * appended by orderBy(column).
     * $strict needs to be set to true if you want to limit sorting only to $sortable array. In such a case,
     * setting any $field definition in the $sortable array to false has no effect, e.g. 'job_title' in the above example,
     * just delete this definition. But if $strict is false, all fields but job_title will be appended to orderBy clause.
     *
     * @param $query
     * @param $definition
     * @param string $dir
     * @param array $sortable
     * @param bool $strict
     * @return mixed
     */
    public function querySorted($query, $definition, $dir = 'asc', $sortable = [], $strict = false)
    {
        if (!array_has($sortable, $definition))
        {
            return ($strict) ? $query : $query->orderBy($definition, $dir);
        }
        else {
            $sortFields = $sortable[$definition];
            if ($sortFields === false) return $query;
            else {
                $return = $query;
                foreach ($sortFields as $sortField) {
                    $return = $return->orderBy($sortField, $dir);
                }
                return $return;
            }
        }
    }

    /**
     * @param $filter
     * @return array|bool|null
     */
    public function buildFilterItem($filter)
    {
        $sibling = $this->config->get('gadget.sign.sibling', '.');
        $lStart = $this->config->get('gadget.sign.list.start', '[');
        $lEnd = $this->config->get('gadget.sign.list.end', ']');
        $sDelimiter = $this->config->get('gadget.sign.delimiter.second', '~');
        $firstDot = strpos($filter, $sibling);
        $secondDot = strpos($filter, $sibling,$firstDot + 1 );
        $firstSquare = strpos($filter, $lStart);
        $secondSquare = strpos($filter, $lEnd,$firstSquare + 1 );

        if (($firstDot === false) || ($secondDot === false) || ($firstSquare === false) || ($secondSquare === false)) return false;
        if ($firstSquare > $secondSquare) return false;
        if ($firstSquare === $secondSquare - 1) return null;

        $field = substr($filter, 0, $firstDot);
        if (empty($field)) return false;

        $operator = substr($filter, $firstDot + 1, $secondDot - $firstDot - 1);
        if (empty($operator)) return false;

        $values = $this->emptyArray(explode($sDelimiter, substr($filter, $firstSquare + 1, $secondSquare - $firstSquare -1)));
        if (empty($values)) return null;

        return [
            'field' => $field,
            'operator' => $operator,
            'values' => $values
        ];
    }

    /**
     * @param $filterString
     * @return array|bool|null
     */
    public function getFilters($filterString)
    {
        if (($filterString == '') || is_null($filterString)) return null;

        $filters = [];
        $items = $this->emptyArray(explode($this->config->get('gadget.sign.delimiter.first', '|'), $filterString));
        foreach ($items as $item)
        {
            $filterItem = $this->buildFilterItem($item);
            if ($filterItem === false) return false;
            if (!is_null($filterItem)) $filters[] = $filterItem;
        }
        return $filters;
    }

    /**
     * General search filter and sort utility
     *
     * The incoming request is analyzed and terms for search, filter and sort are processed
     * on the $query. The $query is built with where clauses and orderBy methods and returned.
     *
     * Use case:
     * General GET requests for lists (searchable, filtered and sorted) e.g. products
     *
     * @param Request $request
     * @param $query
     * @param $searchable
     * @param array $sortable
     * @return mixed
     * @throws InvalidFilterStringException
     * @throws UnknownFilterOperatorException
     */
    public function searchFilterAndSort(Request $request, $query, $searchable, $sortable = [])
    {

        $wFilter = $this->config->get('gadget.word.filter', 'filter');
        $wSearch = $this->config->get('gadget.word.search', 'search');
        $wOrderBy = $this->config->get('gadget.word.order_by', 'sort_by');
        $wDescending = $this->config->get('gadget.word.descending', 'descending');
        $sDelimiter = $this->config->get('gadget.sign.delimiter.second', '~');

        $return = $query;

        $filters = ($request->has($wFilter)) ? $request->query($wFilter) : null;

        $filters = $this->getFilters($filters);
        if ($filters === false) throw new InvalidFilterStringException();

        if (!is_null($filters)) {
            foreach ($filters as $filter) {
                if (!in_array($filter['operator'], ['gt', 'gte', 'lt', 'lte', 'bt', 'in'])) throw new UnknownFilterOperatorException();
                switch ($filter['operator']) {
                    case 'gt': {
                        $return = $return->where($filter['field'], '>', $filter['values'][0]);
                        break;
                    }
                    case 'gte': {
                        $return = $return->where($filter['field'], '>=', $filter['values'][0]);
                        break;
                    }
                    case 'lt': {
                        $return = $return->where($filter['field'], '<', $filter['values'][0]);
                        break;
                    }
                    case 'lte': {
                        $return = $return->where($filter['field'], '<=', $filter['values'][0]);
                        break;
                    }
                    case 'bt': {
                        $return = $return->whereBetween($filter['field'],array($filter['values'][0], $filter['values'][1]));
                        break;
                    }
                    case 'in': {
                        $return = $return->whereIn($filter['field'], $filter['values']);
                        break;
                    }
                    default: break;
                }
            }
        }

        if ($request->has($wSearch)) {
            $search = $this->query($wSearch);
            if ($search != '') {
                $searchItems = explode($sDelimiter, $search);
                foreach ($searchItems as $searchItem) {
                    $return = $return->where(function ($q) use ($searchItem, $searchable) {
                        foreach ($searchable as $column) {

                            if (!($dot = strpos($column,'.')))
                                $q->orWhere($column, 'like', '%' . $searchItem . '%');
                            else
                                $q->orWhereHas(substr($column,0, $dot), function($qs) use($searchItem, $column, $dot) {
                                    $qs->where(substr($column, $dot + 1, strlen($column) - $dot - 1), 'like', '%'. $searchItem .'%');
                                });

                        }
                    });
                }
            }
        }

        if ($request->has($wOrderBy)) {
            if ($request->has($wDescending)) {
                $dir = ($this->isTrue($request->query($wDescending))) ? 'desc' : 'asc';
                $return = $this->querySorted($return, $request->query($wOrderBy), $dir, $sortable);
            }
            else
                $return = $this->querySorted($return, $request->query($wOrderBy), 'asc', $sortable);
        }

        return $return;
    }

    //    public function getFilterDefinitions($baseTable, $filterDefinitions)
//    {
//        $result = [];
//
//        $app_owner_id = \App\User::with('owner')->find(\Illuminate\Support\Facades\Auth::id())->owner->id;
//
//        foreach ($filterDefinitions as $definition) {
//
//            switch ($definition['type']) {
//                case 'text': {
//                    if ($definition['lookup'] == 'values') {
//                        $data = array_pluck(
//                            \Illuminate\Support\Facades\DB::table($baseTable)->where('app_owner_id', $app_owner_id)
//                                ->select($definition['field'])->distinct()->orderBy($definition['field'])->get(),
//                            $definition['field']);
//                        $values = [];
//                        foreach ($data as $d) {
//                            if (!is_null($d)) $values[] = ['label' => $d, 'value' => $d];
//                        }
//                    } else
//                    {
//                        $lookup = explode(':', $definition['lookup']);
//                        if ($lookup[0] === 'table') {
//                            $lookupDefs = explode(",", $lookup[1]);
//                            $array = array_pluck(
//                                \Illuminate\Support\Facades\DB::table($baseTable)->where('app_owner_id', $app_owner_id)
//                                    ->select($definition['field'])->distinct()->orderBy($definition['field'])->get(),
//                                $definition['field']);
//                            $data = \Illuminate\Support\Facades\DB::table($lookupDefs[0])->whereIn($lookupDefs[1], $array)
//                                ->orderBy($lookupDefs[2])->get();
//                            $values = [];
//                            foreach ($data as $item) {
//                                $valueToAdd = [ 'label' => $item->{$lookupDefs[2]}, 'value' => $item->{$lookupDefs[1]}];
//
//                                switch (sizeof($lookupDefs)) {
//                                    case 3:
//                                        $valueToAdd = [ 'label' => $item->{$lookupDefs[2]}, 'value' => $item->{$lookupDefs[1]}];
//                                        break;
//                                    case 4:
//                                        $valueToAdd = [ 'label' => $item->{$lookupDefs[2]}, 'value' => $item->{$lookupDefs[1]}, 'icon' => $item->{$lookupDefs[3]}];
//                                        break;
//                                    case 5:
//                                        $valueToAdd = [ 'label' => $item->{$lookupDefs[2]}, 'value' => $item->{$lookupDefs[1]}, 'icon' => $item->{$lookupDefs[3]}, 'color' => $item->{$lookupDefs[4]}];
//                                        break;
//                                    default:
//                                        break;
//                                }
//
//                                $values[] = $valueToAdd;
//                            }
//                        }
//                    }
//                    break;
//                }
//                case 'boolean': {
//                    $values = [
//                        ['label' => 'True', 'value' => true],
//                        ['label' => 'False', 'value' => false],
//                    ];
//                    break;
//                }
//                case 'numeric': {
//                    $values = null;
//                    break;
//                }
//                default: {
//                    $values = null;
//                }
//            }
//
//            if (is_null($values)) {
//                $result[] = [
//                    'field' => $definition['field'],
//                    'type'  => $definition['type'],
//                    'label' => $definition['label'],
//                ];
//            } else {
//                $result[] = [
//                    'field' => $definition['field'],
//                    'type'  => $definition['type'],
//                    'label' => $definition['label'],
//                    'items' => $values
//                ];
//            }
//
//
//        }
//
//        return $result;
//
//    }
}