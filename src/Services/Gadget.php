<?php
/**
 * Helper functions and request parsing utilities
 *
 */
namespace Yaranliu\Gadget\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Yaranliu\Gadget\Contracts\GadgetContract;
use Yaranliu\Gadget\Exceptions\InvalidFilterStringException;
use Yaranliu\Gadget\Exceptions\InvalidFilterTypeException;
use Yaranliu\Gadget\Exceptions\InvalidFilterValuesDefinitionException;
use Yaranliu\Gadget\Exceptions\RelationNotExistingException;
use Yaranliu\Gadget\Exceptions\UnknownFilterOperatorException;

/**
 * Class Gadget
 *
 * A simple package for a few helper functions and GET request parser.
 *
 * @author Ufuk Yaranlı <ufuk@yaranli.net>
 * @package yaranliu/gadget
 */
class Gadget implements GadgetContract
{

    /**
     * For testing
     *
     * @param $param
     * @return mixed
     */
    public function requestMocked($param)
    {
        return \Illuminate\Support\Facades\Request::input($param);
    }

    /**
     * Returns package configuration
     *
     * @return mixed
     */
    public function configurationDefaults()
    {
        return Config::get('gadget');
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
     * @param int $byte
     * @param int $bit
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
        if (is_string($input)) return explode(Config::get('gadget.sign.delimiter.first', '|'), $input);
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
     * @param $param
     * @return bool
     */
    public function isFalse($param)
    {
        if (is_null($param)) return false;
        if (is_bool($param)) return !$param;
        else return in_array(lowercase($param), ['0', 'no', 'false', 'off', 0, 'hayır', 'yanlış']);
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
     * @param $key
     * @param $default
     * @return array|null|string
     */
    public function inputOrDefault($key, $default)
    {
        if ( Request::has($key) ) {
            return (Request::input($key) === "") ? $default : Request::input($key);
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
     * @param $key
     * @param array $allItems
     * @param array $defaultItems
     * @return array|bool|null|string
     */
    public function keyAsArray($key, array $allItems = array(), array $defaultItems = array())
    {
        $items = $this->inputOrDefault($key, array());

        if (!empty($items))
        {
            if (strtolower($items) === Config::get('gadget.word.all', 'all')) $items = $allItems;
            else {
                $items = explode(Config::get('gadget.sign.delimiter.first', '|'), $items);
                if (!empty(array_diff($items, $allItems))) return false;
            }
        }

        return (empty($items) ? $defaultItems : $items);
    }

    /**
     * Utilizes keyAsArray function to obtain which relations to be loaded by the request.
     *
     * ```RelationNotExistingException``` is  thrown if there is an item in the request param array
     * which is NOT an element of the ```$allItems``` array.
     *
     * <b>Use case</b>:
     *
     * if HTTP Request has a parameter e.g. 'with',  decides which relations are to be loaded by looking at the Model's
     * ```$relations``` (all) and ```$defaultRelations``` (if there is no 'with' parameter on the request)
     *
     * If client sends an HTTP GET request ```?with=all``` all of the relations will be loaded.
     *
     * ```
     * $with = Gadget::loadRelations($request, 'with', $this->allRelations, $this->defaultRelations);
     * $entities = Model::with($with)->all();
     * ```
     *
     * Please note that ```allRelations``` and ```defaultRelations``` properties must be declared as array on the Model class
     *
     * @param array $allRelations
     * @param array $defaultRelations
     * @return array|null|string
     * @throws RelationNotExistingException
     */
    public function with(array $allRelations = array(), array $defaultRelations = array())
    {

        $wWith = Config::get('gadget.word.with', 'with');
        if (Request::has($wWith)) {
            $array = $this->keyAsArray($wWith, $allRelations, $defaultRelations);

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
     * Internal method for building filters
     *
     * @param $filter
     * @return array|bool|null
     */
    public function buildFilterItem($filter)
    {
        $sibling = Config::get('gadget.sign.sibling', '.');
        $lStart = Config::get('gadget.sign.list.start', '[');
        $lEnd = Config::get('gadget.sign.list.end', ']');
        $sDelimiter = Config::get('gadget.sign.delimiter.second', '~');
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
     * Request parser for building filters
     *
     * @param $filterString
     * @return array|bool|null
     */
    public function getFilters($filterString)
    {
        if (($filterString == '') || is_null($filterString)) return null;

        $filters = [];
        $items = $this->emptyArray(explode(Config::get('gadget.sign.delimiter.first', '|'), $filterString));
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
     * @param $query
     * @param $searchable
     * @param array $sortable
     * @return mixed
     * @throws InvalidFilterStringException
     * @throws UnknownFilterOperatorException
     */
    public function searchFilterAndSort($query, array $searchable, array $sortable = [])
    {

        $wFilter = Config::get('gadget.word.filter', 'filter');
        $wSearch = Config::get('gadget.word.search', 'search');
        $wOrderBy = Config::get('gadget.word.order_by', 'sort_by');
        $wDescending = Config::get('gadget.word.descending', 'descending');
        $sDelimiter = Config::get('gadget.sign.delimiter.second', '~');

        $return = $query;

        $filters = (Request::has($wFilter)) ? Request::query($wFilter) : null;

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

        if (Request::has($wSearch)) {
            $search = Request::query($wSearch);
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

        if (Request::has($wOrderBy)) {
            if (Request::has($wDescending)) {
                $dir = ($this->isTrue(Request::query($wDescending))) ? 'desc' : 'asc';
                $return = $this->querySorted($return, Request::query($wOrderBy), $dir, $sortable);
            }
            else
                $return = $this->querySorted($return, Request::query($wOrderBy), 'asc', $sortable);
        }

        return $return;
    }

    /**
     * Gets the filter definitions for the base table to be filtered
     *
     * ```$baseTable``` is the source from where the filter definitions will be resolved.
     *
     * ```$filterDefinitions``` is the array for the structure of the filter to be generated.
     *
     * ```$preFilter``` is the array for pre-filtering the definitions, such as domain filtering, defaults to ```null```
     *
     * ```$limit``` is the max number of values to be retrieved as filter item, defaults to ```20```
     *
     * ####Structure of the ```$filterDefinitions``` parameter
     *
     * ```
     * $filterDefinitions = [
     *      [
     *          'field'     => 'account_type_id',
     *          'label'     => 'Account type',
     *          'type'      => 'string',
     *          'values'    => 'lookup:account_types,id,name,icon,color',
     *      ],
     *      [
     *          'field'     => 'category',
     *          'label'     => 'Category',
     *          'type'      => 'string',
     *          'values'    => 'table',
     *      ],
     *      [
     *          'field'     => 'color',
     *          'label'     => 'Color',
     *          'type'      => 'string',
     *          'values'    => 'list:Red,Orange,Blue',
     *      ]
     *
     *  ];
     *
     * ```
     *
     * ```field```
     *
     * ultimate field to be filtered
     *
     * ```label```
     *
     * display value to be used in the front-end application
     *
     * ```type```
     *
     * data type to be used while evaluating the filter value. *Options* are <string>, <boolean> and <number>
     *
     * ```values```
     *
     * required if ```type``` is ```string```
     *
     * ####Structure of the ```values``` entry in ````filterDefinitions``` parameter array
     *
     * ```table```
     *
     * All the distinct values of ```field``` in the ```$baseTable``` will be retrieved and returned.
     *
     *```lookup:table,linked-field,display-field```
     *
     * The return values for the filter options will be retrieved from a lookup table, e.g. definitions.
     * Two additional fields can be indicated to point the icon and color of the lookup field if exist in the underlying table
     * ```<icon>``` and ```<color>```. See example  above.
     *
     * ```list:value 1,label 1|value 2,label 2|value 3,label 3```
     *
     * ####Structure of the ```$preFilter``` array
     *
     * ```
     * [ 'field1' => 'value1', 'field2' => 'value2', ... ]
     *
     * ```
     *
     * ```$preFilter``` is particularly useful for filtering out the available definitions before retrieving any data from the underlying table.
     * For example, assume that there are multiple companies
     * selling their products using the same application.
     * The products of all companies are stored in ```products``` table with ```company_id``` column
     * referencing to the company which the product belongs to.
     * When the client interface needs to show filter options for the products of a particular company,
     * the lookup definitions need to be filtered before any option sent to the client.
     * So, providing ```['company_id' => <Authenticated user's company id>]``` will show only the filter definitions of the products owned by
     * the authenticated user's company.
     *
     * @param string $baseTable
     * @param array $filterDefinitions
     * @param array $preFilter
     * @param int $limit
     * @return array
     * @throws  InvalidFilterTypeException|InvalidFilterValuesDefinitionException
     */
    public function getFilterDefinitions($baseTable, array $filterDefinitions, $preFilter = array(), $limit = 20)
    {

        $types = array_diff(array_unique(array_column($filterDefinitions, 'type')), ['string', 'number', 'boolean']);

        if (!empty($types)) throw new InvalidFilterTypeException();

        $result = [];
        $values = null;

        foreach ($filterDefinitions as $definition) {
            switch ($definition['type']) {
                case 'boolean': {
                    $values = [
                        ['label' => 'True', 'value' => true],
                        ['label' => 'False', 'value' => false],
                    ];
                    break;
                }
                case 'number': {
                    $values = null;
                    break;
                }
                case 'string': {

                    $valueDefinitions = explode(':', $definition['values']);

                    $query = array();
                    if (in_array($valueDefinitions[0], ['table', 'lookup']))
                    {
                        if (empty($preFilter)) $query =  DB::table($baseTable)->select($definition['field'])->distinct()->orderBy($definition['field'])->limit($limit)->get()->all();
                        else {
                            $query = DB::table($baseTable);
                            foreach ($preFilter as $key => $value) $query = $query->where($key, $value);
                            $query = $query->select($definition['field'])->distinct()->orderBy($definition['field'])->limit($limit)->get()->all();
                        }

                        $query = array_pluck($query, $definition['field']);
                    }
                    $valueDefinitionItems = array();
                    if (in_array($valueDefinitions[0], ['list', 'lookup'])) {
                        $valueDefinitionItems = explode(',', $valueDefinitions[1]);
                    }

                    switch ($valueDefinitions[0]) {
                        case 'table': {
                            $values = [];
                            foreach ($query as $d) {
                                if (!is_null($d)) $values[] = ['label' => $d, 'value' => $d];
                            }
                            break;
                        };
                        case 'lookup': {
                            $array = $query;
                            $data = DB::table($valueDefinitionItems[0])->whereIn($valueDefinitionItems[1], $array)
                                ->orderBy($valueDefinitionItems[2])->get();
                            $values = [];
                            $keys = array('value', 'label', 'icon', 'color');
                            foreach ($data as $item) {
                                $i = 0;
                                $valueToAdd = array();
                                while ($i < count($valueDefinitionItems) - 1)
                                {
                                    $valueToAdd = array_add($valueToAdd, $keys[$i], $item->{$valueDefinitionItems[$i + 1]});
                                    $i++;
                                }

                                $values[] = $valueToAdd;
                            }
                            break;
                        }
                        case 'list': {
                            $values = [];
                            foreach (explode('|', $valueDefinitions[1]) as $items)
                            {
                                $itemArray = explode(',', $items);
                                $values[] = ['value' => $itemArray[0], 'label' => $itemArray[1]];
                            }
                            break;
                        };
                        default: {
                            throw new InvalidFilterValuesDefinitionException();
                            break;
                        }
                    }

                    break;
                }
                default: {
                    break;
                }
            }

            if (is_null($values)) {
                $result[] = [
                    'field' => $definition['field'],
                    'type'  => $definition['type'],
                    'label' => $definition['label'],
                ];
            } else {
                $result[] = [
                    'field' => $definition['field'],
                    'type'  => $definition['type'],
                    'label' => $definition['label'],
                    'items' => $values
                ];
            }


        }

        return $result;

    }

    /**
     * A simplified and faster version of searchFilterAndSort
     *
     * This function is used to search in specific fields of a table with sorting hard coded. No filtering
     * is possible and the nested search is not allowed. The result fields need to be defined as well.
     *
     * Use case:
     * Auto-complete requests from the client app.
     *
     * @param $query
     * @param $searchable
     * @param $sortFields
     * @return mixed
     */
    public function lookupForSelection($query, $searchable, $sortFields)
    {
        $return = $query;

        if (Request::has(Config::get('gadget.word.search', 'search'))) {
            $search = Request::query(Config::get('gadget.word.search', 'search'));
            if ($search != '') {
                $searchItems = explode(Config::get('gadget.sign.delimiter.first', '|'), $search);
                foreach ($searchItems as $searchItem) {
                    $return = $return->where(function ($q) use ($searchItem, $searchable) {
                        foreach ($searchable as $column) {
                            $q->orWhere($column, 'like', '%' . $searchItem . '%');
                        }
                    });
                }
            }
        }

        foreach ($sortFields as $field => $dir) $return = $return->orderBy($field, $dir);

        return $return;
    }

    // DB Utils
    // ----------------

    /**
     * Gets the number of records in a table belonging to a domain
     * and generates a unique key by padding string to a specified length.
     *
     * Use case:
     * Generating the product reference which is required by the database engine
     * and supposed to be unique before actually storing the row into the table.
     *
     * @param $table
     * @param string $forKey
     * @param null $userDomainId
     * @param string $domainKey
     * @param int $padLength
     * @param string $padString
     * @return string
     */
    public function autoReference($table, $forKey = "reference", $userDomainId = null, $domainKey = "domain_id", $padLength = 10, $padString = "0")
    {
        $query = (is_null($userDomainId)) ? DB::table($table) : DB::table($table)->where($domainKey, $userDomainId);
        $r = $query->get()->count() + 1;
        $ref = str_pad($r, $padLength, $padString, STR_PAD_LEFT);
        $i = 1;
        $existing = $query->where($forKey, $ref)->get();
        while ($existing->count() > 0)
        {
            $ref = $ref."-".$i;
            $existing = $query->where($forKey, $ref)->get();
            $i++;
        }

        return $ref;
    }

    // --------------------
    // Validation utilities
    // --------------------

    /**
     * Generates an array for validation with Model's $fillable attributes and
     * sets them to 'sometimes' validation rule.
     *
     * $request->validate([]) filters out the non-listed attributes. This function is used to
     * add the remaining attributes to the validation array.
     *
     * e.g.
     * $data = $request->validate(addFillables(['name' => 'required'], Product::class));
     *
     * @param array $validate
     * @param $class
     * @param array $except
     * @return array
     */
    public function addFillables(array $validate, $class, array $except = [])
    {

        $object = new $class();

        $return = $validate;

        $fillables = $object->getFillable();

        foreach ($fillables as $fillable) {
            if (!in_array($fillable, $except)) $return = array_add($return, $fillable, 'sometimes');
        }

        return $return;
    }

    //---------------------
    // Pagination Utilities
    //---------------------

    /**
     * Looks for and returns per_page parameter in the request,
     * otherwise returns per_page value in the \config\api.php configuration file
     *
     * @return mixed
     */
    public function  calc_per_page()
    {
        return $this->inputOrDefault(API_WORD_PER_PAGE, API_PER_PAGE);
    }

    /**
     * @param $query
     * @param $perPage
     * @return mixed
     */
    public function getPaginated($query, $perPage)
    {
        if ($perPage == 0) return $query->get();
        else return $query->paginate($perPage);
    }

}