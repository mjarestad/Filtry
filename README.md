#Filtry

[![Latest Stable Version](https://poser.pugx.org/mjarestad/filtry/v/stable)](https://packagist.org/packages/mjarestad/filtry)
[![Build Status](https://api.travis-ci.org/mjarestad/Filtry.svg)](https://api.travis-ci.org/mjarestad/Filtry)
[![License](https://poser.pugx.org/mjarestad/filtry/license)](https://packagist.org/packages/mjarestad/filtry)

A package to filter and sanitize input data in Laravel 4/5 or as a standalone package.
This is perfect to use with the Laravel 4 Validation class to filter data before validation.
You can easily extend and create your own custom filters.

##Requirements
php version > 5.6

##Installation

```bash
composer require mjarestad/filtry
```

### Laravel

Add the ServiceProvider to the providers array in `app/config/app.php`

```
Laravel 5:
Mjarestad\Filtry\FiltryServiceProviderLaravel5::class,

Laravel 4:
'Mjarestad\Filtry\FiltryServiceProvider',
```

Add the Facade to the aliases array in `app/config/app.php`

```
'Filtry'  => 'Mjarestad\Filtry\Facades\Filtry',
```

##Usage

###Laravel 5

####Form Requests

Extend your Form Request Validation classes with the provided Filtry Request to filter input data before validation.

```php
<?php

use Mjarestad\Filtry\Http\Requests\Request;

class StorePostRequest extends Request
{
    public function rules()
    {
        return [
            'author' => 'required',
            'slug'   => 'required',
        ];
    }

    public function filters()
    {
        return [
            'author' => 'trim|ucwords',
            'slug'   => 'trim|replace:haystack,needle|slug',
        ];
    }
}
```

###Laravel 4

Add a the filters property to your Eloquent Model or anywhere else you prefer.

```php
<?php

class Post extends Eloquent {

    public static $filters = array(
        'author' => 'trim|ucwords',
        'slug'   => 'trim|replace:haystack,needle|slug'
    );

    public static $rules = array(
        'author' => 'required',
        'slug'   => 'required'
    );
}
```

In your controller or service call `Filtry::make()` and provide the data to filter and your filters array.

```php
<?php

$filtry = Filtry::make(Input::all(), Post::$filters);
```

To get the filtered value use `$filtry->getFiltered()`

```php
<?php

$validator = Validator::make($filtry->getFiltered(), Post::$rules);
```

To get the unfiltered values, use:

```php
<?php

$filtry->getOld();
```

Every method can be used to filter a single value.

```php
<?php

Filtry::trim('some string');
Filtry::slug('some string');
Filtry::snakeCase('some string');
```

###Standalone

```php
<?php

$filters = [
    'author' => 'trim|ucwords',
    'slug'   => 'trim|replace:haystack,needle|slug',
];

$data = [
    'author' => 'John Doe',
    'slug'   => 'My post title',
];

$filtry = new Mjarestad\Filtry\Filtry;
$filtry->make($data, $filters);
$filteredData = $filtry->getFiltered();
```

##Create custom filters

###Laravel

Extend with custom filters to use in `Filtry::make()` or as dynamic methods.

```php
<?php

Filtry::extend('my_custom_filter', function ($data) {
    return str_replace('-', '_', $data);
});
```

Call the extended filter dynamically

```php
<?php

Filtry::myCustomFilter('some-custom-string');
```

#### Optional parameters

You can define optional parameters for your filter.

```php
<?php

Filtry::extend('custom_filter', function ($data, $param1, $param2) {
    return $data . ($param1 + $param2);
});
```

And then add the parameters in your request:

```php
<?php

use Mjarestad\Filtry\Http\Requests\Request;

class StorePostRequest extends Request
{
    public function rules()
    {
        return [
            'author' => 'required',
        ];
    }

    public function filters()
    {
        return [
            'author' => 'custom_filter:1,2',
        ];
    }
}
```

It will concatenate 3 to your author attribute.

###Standalone

```php
<?php

$filtry = new Mjarestad\Filtry\Filtry;

$filtry->extend('my_custom_filter', function ($data) {
    return str_replace('-', '_', $data);
});

$filtry->myCustomFilter('some-custom-string');
```

##Available filters

###Core PHP filters

* trim
* ltrim
* rtrim
* lower (strtolower)
* upper (strtoupper)
* ucfirst
* ucwords
* stripslashes
* replace:search,replace (str_replace)

###Custom filters

* xss_clean - clean string with htmlspecialchars
* strip_whitespaces - strip all white spaces
* strip_dashes - strip all dashes
* slug - makes string url-friendly
* prep_url - adds http:// if not present

###Laravel filters

* snake_case
* camel_case
* studly_case

> Theese filters can still be used in a non-Laravel application.
