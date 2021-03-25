<p align="center">
</p>
<p align="center">
<a href="https://travis-ci.com/mav3rick177/rapid-pagination"><img src="https://travis-ci.com/mav3rick177/rapid-pagination.svg?branch=main" alt="Build Status"></a>
<a href="https://coveralls.io/github/mav3rick177/rapid-pagination?branch=main"><img src="https://coveralls.io/repos/github/mav3rick177/rapid-pagination/badge.svg?branch=main" alt="Coverage Status"></a>
<a href="https://scrutinizer-ci.com/g/mav3rick177/rapid-pagination?branch=main"><img src="https://scrutinizer-ci.com/g/mav3rick177/rapid-pagination/badges/quality-score.png?b=main" alt="Scrutinizer Code Quality"></a>
</p>


# Rapid Pagination for Laravel

Rapid pagination without using OFFSET


## Requirements

- PHP: ^7.1
- Laravel: ^5.5 || ^6.0 || ^7.0 || ^8.0

## Installing

```bash
composer require mav3rick177/rapid-pagination
```

Register service provider.

`config/app.php`:

```php
        /*
         * Package Service Providers...
         */
        mav3rick177\RapidPagination\MacroServiceProvider::class,
```
## Basic Usage 1

### Simple pagination for users table

### Create routes

```php
Route::post('/users', [UserController::class, 'index'])->name('users.list');
Route::get('/users', [UserController::class, 'index'])->name('users.list');
```

### Controller

`init_paginator_cache($fields)` takes the list of form field names and returns an array which contains field as key and value pairs, this cache is used to hold form values between pages!

```php
class UserController extends Controller
{
    ...
    public function index()
    {
        // Form field names
        $formFields = ['sort', 'perPage', 'from', 'to', 'cursor']; 

        // Cache Form values
        $cache = init_rapid_paginator_cache($formFields);
        
        // Extract values from the cache 
        $sort = isset($cache['sort']) ? $cache['sort'] : '>';
        $perPage = isset($cache['perPage']) ? $cache['perPage'] : 10;
        $from = isset($cache['from']) ? $cache['from'] : null;
        $to = isset($cache['to']) ? $cache['to'] : null;
        $field = isset($cache['cursor']) ? $cache['cursor'] : 'id';

        // Columns
        $columns = ['id','name','email', 'dob']; 

        // field to use as a cursor
        $cursor = $field; 

        /*
        ** Query
        */

        $query = User::select($columns);
                   // ->where('email', 'like', '%example.net%');
       
        //Get Users Where Birth Date of Birth is between $from and $to 
        if($cursor == 'dob' && $from != null && $to != null)
            $query = $query->whereBetween('dob', [$from, $to]);
        

        // Call our custom paginator here...
        $result = rapid_paginator($query, $field, $cache, $sort, $perPage);
        
        // return the results
        return view('users')->with($result);
    }
    ...
```

`rapid_paginator()` returns array with 2 entries
- 'items': Rapid Paginator object which contains Model objects as well as other informations such as the cursor and some booleans to check if in the current 'state' we can navigate to the previous or next page... in our example we use to iterate over it to get the list of users (in the page) also to encode other informations in the 'state' 
- 'cache': contains form values...

## Render the pagination

in the view add

```php
{{ $items->render() }}
```

where $items is the Rapid Paginator object...

## Basic Usage 2 


Then you can chain `->rapid_pagination()` method from Query Builder, Eloquent Builder and Relation.

```php
$cursor = [
    'id' => 3,
    'created_at' => '2017-01-10 00:00:00',
    'updated_at' => '2017-01-20 00:00:00',
];

$result = App\Post::whereUserId(1)
    ->rapid_pagination()
    ->forward()
    ->limit(5)
    ->orderByDesc('updated_at') // ORDER BY `updated_at` DESC, `created_at` DESC, `id` DESC
    ->orderByDesc('created_at')
    ->orderByDesc('id')
    ->seekable()
    ->paginate($cursor)
    ->toJson(JSON_PRETTY_PRINT);
```

It will run the optimized query.


```sql
(

    SELECT * FROM `posts`
    WHERE `user_id` = 1
    AND (
        `updated_at` = '2017-01-20 00:00:00' AND `created_at` = '2017-01-10 00:00:00' AND `id` > 3
        OR
        `updated_at` = '2017-01-20 00:00:00' AND `created_at` > '2017-01-10 00:00:00'
        OR
        `updated_at` > '2017-01-20 00:00:00'
    )
    ORDER BY `updated_at` ASC, `created_at` ASC, `id` ASC
    LIMIT 1

) UNION ALL (

    SELECT * FROM `posts`
    WHERE `user_id` = 1
    AND (
        `updated_at` = '2017-01-20 00:00:00' AND `created_at` = '2017-01-10 00:00:00' AND `id` <= 3
        OR
        `updated_at` = '2017-01-20 00:00:00' AND `created_at` < '2017-01-10 00:00:00'
        OR
        `updated_at` < '2017-01-20 00:00:00'
    )
    ORDER BY `updated_at` DESC, `created_at` DESC, `id` DESC
    LIMIT 6

)
```

And you'll get


```json
{
  "records": [
    {
      "id": 3,
      "user_id": 1,
      "text": "foo",
      "created_at": "2017-01-10 00:00:00",
      "updated_at": "2017-01-20 00:00:00"
    },
    {
      "id": 5,
      "user_id": 1,
      "text": "bar",
      "created_at": "2017-01-05 00:00:00",
      "updated_at": "2017-01-20 00:00:00"
    },
    {
      "id": 4,
      "user_id": 1,
      "text": "baz",
      "created_at": "2017-01-05 00:00:00",
      "updated_at": "2017-01-20 00:00:00"
    },
    {
      "id": 2,
      "user_id": 1,
      "text": "qux",
      "created_at": "2017-01-17 00:00:00",
      "updated_at": "2017-01-18 00:00:00"
    },
    {
      "id": 1,
      "user_id": 1,
      "text": "quux",
      "created_at": "2017-01-16 00:00:00",
      "updated_at": "2017-01-18 00:00:00"
    }
  ],
  "has_previous": false,
  "previous_cursor": null,
  "has_next": true,
  "next_cursor": {
    "updated_at": "2017-01-18 00:00:00",
    "created_at": "2017-01-14 00:00:00",
    "id": 6
  }
}
```

## Resource Collection

Rapid Pagination supports Laravel's API Resources.

- [Eloquent: API Resources - Laravel - The PHP Framework For Web Artisans](https://laravel.com/docs/6.x/eloquent-resources)

Use helper traits on Resource and ResourceCollection.

```php
use Illuminate\Http\Resources\Json\JsonResource;
use mav3rick177\RapidPagination\RapidPaginationResourceTrait;

class PostResource extends JsonResource
{
    use RapidPaginationResourceTrait;
}
```

```php
use Illuminate\Http\Resources\Json\ResourceCollection;
use mav3rick177\RapidPagination\RapidPaginationResourceCollectionTrait;

class PostResourceCollection extends ResourceCollection
{
    use RapidPaginationResourceCollectionTrait;
}
```

```php
$posts = App\Post::rapid_pagination()
    ->orderByDesc('id')
    ->paginate();

return new PostResourceCollection($posts);
```

```json5
{
  "data": [/* ... */],
  "has_previous": false,
  "previous_cursor": null,
  "has_next": true,
  "next_cursor": {/* ... */}
}
```

## Classes

| Name | Type | Parent Class | Description |
|:---|:---|:---|:---|
| mav3rick177\\RapidPagination\\`Paginator` | Class | mav3rick177\RapidPagination\\Base\\`Paginator` | Fluent factory implementation for Laravel |
| mav3rick177\\RapidPagination\\`Processor` | Class | mav3rick177\RapidPagination\\Base\\`AbstractProcessor` | Processor implementation for Laravel |
| mav3rick177\\RapidPagination\\`PaginationResult` | Class | mav3rick177\RapidPagination\\Base\\`PaginationResult` | PaginationResult implementation for Laravel |
| mav3rick177\\RapidPagination\\`MacroServiceProvider` | Class | Illuminate\\Support\\`ServiceProvider` | Enable macros chainable from QueryBuilder, ElqouentBuilder and Relation |
| mav3rick177\\RapidPagination\\`RapidPaginationResourceTrait` | Trait | | Support for Laravel JsonResource |
| mav3rick177\\RapidPagination\\`RapidPaginationResourceCollectionTrait` | Trait | | Support for Laravel ResourceCollection |

`Paginator`, `Processor` and `PaginationResult` are macroable.

## API

### Paginator::__construct()<br>Paginator::create()

Create a new paginator instance.  
If you use Laravel macros, however, you don't need to directly instantiate.

```php
static Paginator create(QueryBuilder|EloquentBuilder|Relation $builder): static
Paginator::__construct(QueryBuilder|EloquentBuilder|Relation $builder)
```

- `QueryBuilder` means `\Illuminate\Database\Query\Builder`
- `EloquentBuilder` means `\Illuminate\Database\Eloquent\Builder`
- `Relation` means `\Illuminate\Database\Eloquent\Relation`

### Paginator::transform()

Transform RapidPagination Query into Illuminate builder.

```php
Paginator::transform(Query $query): QueryBuilder|EloquentBuilder|Relation
```

### Paginator::build()

Perform configure + transform.

```php
Paginator::build(\mav3rick177\RapidPagination\Base\Contracts\Cursor|array $cursor = []): QueryBuilder|EloquentBuilder|Relation
```

### Paginator::paginate()

Perform configure + transform + process.

```php
Paginator::paginate(\mav3rick177\RapidPagination\Base\Cursor|array $cursor = []): \mav3rick177\RapidPagination\PaginationResult
```

#### Arguments

- **`(mixed)`** __*$cursor*__<br> An associative array that contains `$column => $value` or an object that implements `\mav3rick177\RapidPagination\Base\Contracts\Cursor`. It must be **all-or-nothing**.
  - For initial page, omit this parameter or pass empty array.
  - For subsequent pages, pass all parameters. Partial parameters are not allowd.

#### Return Value

e.g. 

(Default format when using `\Illuminate\Database\Eloquent\Builder`)

```php
object(mav3rick177\RapidPagination\PaginationResult)#1 (5) {
  ["records"]=>
  object(Illuminate\Database\Eloquent\Collection)#2 (1) {
    ["items":protected]=>
    array(5) {
      [0]=>
      object(App\Post)#2 (26) { ... }
      [1]=>
      object(App\Post)#3 (26) { ... }
      [2]=>
      object(App\Post)#4 (26) { ... }
      [3]=>
      object(App\Post)#5 (26) { ... }
      [4]=>
      object(App\Post)#6 (26) { ... }
    }
  }
  ["hasPrevious"]=>
  bool(false)
  ["previousCursor"]=>
  NULL
  ["hasNext"]=>
  bool(true)
  ["nextCursor"]=>
  array(2) {
    ["updated_at"]=>
    string(19) "2017-01-18 00:00:00"
    ["created_at"]=>
    string(19) "2017-01-14 00:00:00"
    ["id"]=>
    int(6)
  }
}
```

### Paginator::useFormatter()<br>Paginator::restoreFormatter()<br>Paginator::process()

Invoke Processor methods.

```php
Paginator::useFormatter(Formatter|callable $formatter): $this
Paginator::restoreFormatter(): $this
Paginator::process(\mav3rick177\RapidPagination\Base\Query $query, \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $rows): \mav3rick177\RapidPagination\PaginationResult
```

### PaginationResult::toArray()<br>PaginationResult::jsonSerialize()

Convert the object into array.

**IMPORTANT: `camelCase` properties are converted into `snake_case` form.**

```php
PaginationResult::toArray(): array
PaginationResult::jsonSerialize(): array
```

### PaginationResult::__call()

Call macro or Collection methods.

```php
PaginationResult::__call(string $name, array $args): mixed
```

e.g.

```php
PaginationResult::macro('foo', function () {
    return ...;
});
$foo = $result->foo();
```

```php
$first = $result->first();
```

### `Note: this is a lampager modification` 
[lampager/lampager](https://github.com/lampager/lampager) <br>
[lampager/lampager-laravel](https://github.com/lampager/lampager-laravel)