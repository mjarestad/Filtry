Filter
==============

Filter and sanitize input data

##Installation
Install with composer.

    "require": {
        "mjarestad/laravel-filter": "dev-master"
    }
    
###Laravel 4

Add the ServiceProvider to the provider array in app/config/app.php

    'Pixel\Filter\FilterServiceProvider',
    
Add the Facade to the aliases array in app/config/app.php

    'Filter'  => 'Pixel\Filter\FilterFacade',

##Usage

###Laravel 4

Add a the filters property to your Eloquent Model or anywhere else you prefer.

    class Link extends Eloquent {
    
        public static $filters = array(
            'name' => 'trim|ucfirst',
            'slug' => 'trim|clean_url',
            'url'  => 'trim|prep_url'
        );
        
        public static $rules = array(
            'name' => 'required',
            'slug' => 'required',
            'url'  => 'required'
        );
        
    }
    
And in your controller call Filter::make() and provide the data to filter and your filters array.

    $filter = Filter::make(Input::all(), Link::$filters);
    
To get the filtered value use $filter->getFiltered()

    $validator = Validator::make($filter->getFiltered(), Link::$rules);
    
To get the unfiltered values back, use:

    $filter->getOld();
    
Every filter can be used like this...

    Filter::trim('some string');
    Filter::slug('some string');
    
###Standalone

    $filters = array(
        'name' => 'trim|ucfirst',
        'slug' => 'trim|clean_url',
        'url'  => 'trim|prep_url'
    );
    
    $data = array(
        'name' => 'Google',
        'slug' => 'Google link',
        'url'  => 'www.google.se'
    );
    
    $filter = new Pixel\Filter;
    $filter->make($data, $filters);
    $filteredData = $filter->getFiltered();
    
##Create custom filters

Extend with custom filters to use in Filter::make() or as dynamic methods.

    Filter::extend('my_custom_filter', function($data){
        return str_replace('-', '_', $data);
    });
    
Call the extended filter dynamically

    Filter::myCustomFilter('some-custom-string');
    
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

###Custom filters

* xss_clean - clean string with htmlspecialchars
* remove_whitespace - removes all white spaces
* slug - makes string url-friendly
* prep_url - adds http:// if not present

###Laravel 4 specific filters

* snake_case
* camel_case
* studly_case
