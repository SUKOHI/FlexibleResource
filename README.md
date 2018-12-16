# FlexibleResource
A Laravel package that allows you to flexibly generate resource.  
This package is maintained under L5.7.

# Installation

    composer require sukohi/flexible-resource:1.*

# Preparation

Set `FlexibleResourceTrait` like so.

    use Sukohi\FlexibleResource\Traits\FlexibleResourceTrait;
    
    class ResourceController extends Controller
    {
        use FlexibleResourceTrait;

Also Routing for the controller.

    Route::get('resource', 'ResourceController@get');

# Usage

## Basic usage

Add private method(s) to your controller.

    class ResourceController extends Controller
    {
        use FlexibleResourceTrait;
    
        private function userTypes() {
    
            return [
               1 => 'admin',
               2 => 'owner',
               3 => 'user'
           ];
    
        }

In this case, you can get `userTypes` through the following URL.

    https://example.com/resource?keys=userTypes

Example:

    {
        "userTypes":{
            "1":"admin",
            "2":"owner",
            "3":"user"
        }
    }

## Multiple methods

Of course, you also can set multiple methods like this.

    class ResourceController extends Controller
    {
        use FlexibleResourceTrait;
    
        private function userTypes() {
    
            // ...
    
        }
        
        private function userNames() {
    
            // ...
    
        }
        
In this case, you need to join some keys with `|`.

    https://example.com/resource?keys=userTypes|userNames

## with Arguments

This package supports arguments for each method.
If you'd like to call a method which need to 3 arguments, set parameters.

    private function yourMethod($value_1, $value_2, $value_3) {

        // ...

    }

For your information, you can set default value.

    private function yourMethod($value_1 = null, $value_2 = 10, $value_3 = 1000) {

        // ...

    }

URL:

    https://example.com/resource?keys=userTypes:value1,value2,value3

# Conversion to collection

If your controller already has a method called `userTypes`, `userTypeCollection` is also automatically available.

In this case resource data will be converted to collection like so.
    
    {
        "userTypeCollection":[
            {"key":1, "value":"admin"},
            {"key":2, "value":"owner"},
            {"key":3, "value":"user"}
        ]
    }
        
In addition, you can change keys through `$auto_collections`.

    class ResourceController extends Controller
    {
        protected $auto_collections = [
            'userTypes' => ['id' => 'type']
        ];

As a result, resource data is like so.

    {
        "userTypeCollection":[
            {"id":1, "type":"admin"},
            {"id":2, "type":"owner"},
            {"id":3, "type":"user"}
        ]
    }

with Vue.js

If you'd like to get your resource data in Vue.js, a dedicated package called [v-flexible-resource](https://github.com/SUKOHI/v-flexible-resource) is available.

# License

This package is licensed under the MIT License.

Copyright 2018 Sukohi Kuhoh