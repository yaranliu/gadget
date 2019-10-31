# Gadget
A simple package for a few helper functions and GET request parser for Laravel framework

## Installation
```composer require yaranliu/gadget```
## Available Methods

### Configuration

#### Gadget::configurationDefaults()
Returns package configuration

#### Gadget::emptyArray(array $arrayToClean, $string = true, $array = true)
Removes empty strings and/or arrays from a plain array

#### Gadget::aggregateArray(array $input, $key)
Groups the array by `$key` and aggregates (add or multiply) numerical values  

#### Gadget::setBit($word, $bit)
Sets *nth* `$bit` of `$word` to 1

#### Gadget::resetBit($word, $bit)
Sets *nth* `$bit` of `$word` to 0

#### Gadget::checkBit($word, $bit)
Returns *nth* `$bit` of the `$word` as `true` i.e. `1` or `false` i.e. `0`
 
#### Gadget::cArray($input) 
If $input is an array, returns $input.
If $input is a string, returns array of exploding the string with the delimiter defined in configuration file

#### Gadget::removeFromArray($item, array $array)
Removes a $item from $array and returns the $array

#### Gadget::reduceSpaces($text, $trim = true)
Removes extra spaces in the text and leaves only one space between the words,
optionally trims the $text

#### Gadget::tr_strtolower($text)
Converts $text to lowercase using Turkish character set and multi byte conversion

#### Gadget::tr_strtoupper($text)
Converts $text to uppercase using Turkish character set and multi byte conversion

#### Gadget::tr_ucfirst($text, $reduceSpaces = true, $trim = true)
Converts $text to lowercase and capitalizes each word's first letter,
optionally removing extra spaces between words and $trims the $text.

#### Gadget::lowercase($text, $locale = 'tr')
Converts $text to lowercase depending on $locale (default is 'tr')
tr_strtolower is used if $locale is 'tr';  otherwise, mb_strtolower is used

#### Gadget::uppercase($text, $locale = 'tr')
Converts $text to uppercase depending on $locale (default is 'tr').
tr_strtoupper is used if $locale is 'tr';  otherwise, mb_strtoupper is used

#### Gadget::isTrue($param, $base = array(), $default = true, $locale = 'tr')
Interprets $input as boolean true or false.
Checks if $input is an item of the following:
'1', 'yes', 'true', 'on', 1, 'evet', 'doğru'

If provided, the items of $base will be interpreted as True.
Providing $default as false will use only $base for interpretation

$locale is used for case-insensitive check and defaults to 'tr' (Turkish)

#### Gadget::isFalse($param)


#### Gadget::dotToArray($item, $array = array())
Converts dotted string $item to an associative array or appends to the provided $array

```
$array = dotToArray('tags.name');
$array = dotToArray('tags.color', $array);
$array = dotToArray('tags.color', $array);       // Duplicate
$array = dotToArray('pictures.name', $array);
$array = dotToArray('pictures.title', $array);

$array = ['tags' => ['name', 'color'],  'picture' => ['name', 'title']]
```

#### Gadget::inputOrDefault($key, $default)
Returns the request parameter's value or the default value

#### Gadget::keyAsArray($key, array $allItems = array(), array $defaultItems = array())
Explodes the request parameter `$key` into an array and returns this array.
If the `$key` does not exist on the request then the `$defaultItems` (array) is returned.
If $key is 'all', $allItems is returned.

If there is an item in the request param array which is NOT an element of the $allItems array, FALSE is returned

#### Gadget::with(array $allRelations = array(), array $defaultRelations = array())
Utilizes keyAsArray function to obtain which relations to be loaded by the request.

 ```RelationNotExistingException``` is  thrown if there is an item in the request param array
which is NOT an element of the ```$allItems``` array.
*Use case*

if HTTP Request has a parameter e.g. 'with',  decides which relations are to be loaded by looking at the Model's
```$relations``` (all) and ```$defaultRelations``` (if there is no 'with' parameter on the request)

If client sends an HTTP GET request ```?with=all``` all of the relations will be loaded.

```
$with = Gadget::loadRelations($request, 'with', $this->allRelations, $this->defaultRelations);
$entities = Model::with($with)->all();
```

Please note that ```allRelations``` and ```defaultRelations``` properties must be declared as array on the Model class
 
#### Gadget::querySorted($query, $definition, $dir = 'asc', $sortable = [], $strict = false)
Adds sorting to and returns provided $query.
*Example for $sortable argument:*
```
 public static $sortable = [
          'full_name' => ['title', 'name'],
          'name'  => ['name']
          'job_title' => false,
      ];
```
If there are more than one column to be sorted according to $definition argument, these columns must exist in the array
and will be appended to the orderBy clause.

If you want to exclude $field from sorting anyway, provide false for $field definition in $sortable array.

If $field is not in $sortable and $strict is false, $field will be treated a column name and $query will simply be
appended by orderBy(column).
$strict needs to be set to true if you want to limit sorting only to $sortable array. In such a case,
setting any $field definition in the $sortable array to false has no effect, e.g. 'job_title' in the above example,
just delete this definition. But if $strict is false, all fields but job_title will be appended to orderBy clause.
 
#### Gadget::buildFilterItem($filter)
Internal method for building filters

#### Gadget::getFilters($filterString)
Request parser for building filters

#### Gadget::searchFilterAndSort($query, array $searchable, array $sortable = [])
General search filter and sort utility

The incoming request is analyzed and terms for search, filter and sort are processed
on the $query. The $query is built with where clauses and orderBy methods and returned.

*Use case:*
General GET requests for lists (searchable, filtered and sorted) e.g. products

#### Gadget::getFilterDefinitions($baseTable, array $filterDefinitions, $preFilter = array(), $limit = 20)
Gets the filter definitions for the base table to be filtered
```$baseTable``` is the source from where the filter definitions will be resolved.
```$filterDefinitions``` is the array for the structure of the filter to be generated.
```$preFilter``` is the array for pre-filtering the definitions, such as domain filtering, defaults to ```null```
```$limit``` is the max number of values to be retrieved as filter item, defaults to ```20```

*Structure of the* ```$filterDefinitions``` *parameter*
```
$filterDefinitions = [
     [
         'field'     => 'account_type_id',
         'label'     => 'Account type',
         'type'      => 'string',
         'values'    => 'lookup:account_types,id,name,icon,color',
     ],
     [
         'field'     => 'category',
         'label'     => 'Category',
         'type'      => 'string',
         'values'    => 'table',
     ],
     [
         'field'     => 'color',
         'label'     => 'Color',
         'type'      => 'string',
         'values'    => 'list:Red,Orange,Blue',
     ]

 ];

```

```field```

ultimate field to be filtered

```label```

display value to be used in the front-end application

```type```

data type to be used while evaluating the filter value. *Options are <string>, <boolean> and <number>

```values```

required if ```type``` is ```string```

*Structure of the* ```values``` *entry in* ```filterDefinitions``` *parameter array*

```table```

All the distinct values of ```field``` in the ```$baseTable``` will be retrieved and returned.

```lookup:table,linked-field,display-field```

The return values for the filter options will be retrieved from a lookup table, e.g. definitions.
Two additional fields can be indicated to point the icon and color of the lookup field if exist in the underlying table
```<icon>``` and ```<color>```. See example  above.

```list:value 1,label 1|value 2,label 2|value 3,label 3```

*Structure of the* ```$preFilter``` *array*

```
[ 'field1' => 'value1', 'field2' => 'value2', ... ]

```

```$preFilter``` is particularly useful for filtering out the available definitions before retrieving any data from the underlying table.
For example, assume that there are multiple companies
selling their products using the same application.
The products of all companies are stored in ```products``` table with ```company_id``` column
referencing to the company which the product belongs to.
When the client interface needs to show filter options for the products of a particular company,
the lookup definitions need to be filtered before any option sent to the client.
So, providing ```['company_id' => <Authenticated user's company id>]``` will show only the filter definitions of the products owned by
the authenticated user's company. 

#### Gadget::lookupForSelection($query, $searchable, $sortFields)
A simplified and faster version of searchFilterAndSort

This function is used to search in specific fields of a table with sorting hard coded. No filtering
is possible and the nested search is not allowed. The result fields need to be defined as well.

*Use case:*
Auto-complete requests from the client app.
 

*DB Utils*

#### Gadget::autoReference($table, $forKey = "reference", $userDomainId = null, $domainKey = "domain_id", $padLength = 10, $padString = "0")
Gets the number of records in a table belonging to a domain
and generates a unique key by padding string to a specified length.

*Use case:*
Generating the product reference which is required by the database engine
and supposed to be unique before actually storing the row into the table.

*Validation utilities*

#### Gadget::addFillables(array $validate, $class, array $except = [])
Generates an array for validation with Model's ```$fillable``` attributes and
sets them to 'sometimes' validation rule.
```$request->validate([])``` filters out the non-listed attributes. This function is used to
add the remaining attributes to the validation array.

e.g.
```$data = $request->validate(addFillables(['name' => 'required'], Product::class));```

*Pagination Utilities*

#### Gadget:: calc_per_page()
Looks for and returns per_page parameter in the request,
otherwise returns per_page value in the \config\api.php configuration file

#### Gadget::getPaginated($query, $perPage)
