Filter
==============

Filter and sanitize input data

##Installation
Install through composer

    "require": {
        "mjarestad/laravel-filter": "dev-master"
    }
    
###Laravel specific instructions

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

###Laravel filters

* snake_case
* camel_case
* studly_case

###Custom filters

* xss_clean
* remove_whitespace
* clean_url
* prep_url

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
