Filter
==============

Filter and sanitize input data

##Installation
Install through composer

    "require": {
        "mjarestad/laravel-filter": "dev-master"
    }
    
###In Laravel 4

Add the ServiceProvider to the provider array in app/config/app.php

    'Pixel\Filter\FilterServiceProvider',
    
Add the Facade to the aliases array in app/config/app.php

    'Filter'  => 'Pixel\Filter\FilterFacade',

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

##Usage

Add a the filter property to your Eloquent Model.

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
    
And in your controller.

    $filter = Filter::make(Input::all(), Link::$filters);
    $validator = Validator::make($filter->getFiltered(), Link::$rules);
    
To get the unfiltered values back, use:

    $filter->getOld();
    
##Create custom filters

Extend with custom filters to use in Filter::make() or as dynamic methods.

    Filter::extend('my_custom_filter', function($data){
        return str_replace('-', '_', $data);
    });
    
Call the extended filter dynamically

    Filter::myCustomFilter('some-custom-string');
