laravel-filter
==============

Filter and sanitize input data before validation

##Installation
Install through composer

    "require": {
        "mjarestad/laravel-filter": "dev-master"
    }

Add the ServiceProvider to the provider array in app/config/app.php

    'Pixel\Filter\FilterServiceProvider',
    
Add the Facade to the aliases array in app/config/app.php

    'Filter'  => 'Pixel\Filter\FilterFacade',
