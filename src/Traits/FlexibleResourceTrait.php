<?php

namespace Sukohi\FlexibleResource\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationRuleParser;

trait FlexibleResourceTrait {

    public function get(Request $request) {

        $resources = [];
        $keys = explode('|', $request->keys);

        foreach($keys as $key) {

            [$method, $args] = ValidationRuleParser::parse($key);
            $method = Str::camel($method);

            if(method_exists($this, $method)) {

                $resources[$method] = call_user_func_array([$this, $method], $args);

            } else if(Str::endsWith($method, 'Collection')) {

                $original_method = $this->getOriginalMethod($method);

                if(method_exists($this, $original_method)) {

                    $key_name = 'key';
                    $value_name = 'value';

                    if(isset($this->auto_collections)) {

                        $auto_collection_keys = array_keys($this->auto_collections);

                        if(in_array($original_method, $auto_collection_keys) ||
                            in_array('*', $auto_collection_keys)) {

                            $auto_collection = (in_array($original_method, $auto_collection_keys))
                                ? $auto_collection = $this->auto_collections[$original_method]
                                : $auto_collection = $this->auto_collections['*'];
                            list($collection_keys, $collection_values) = Arr::divide($auto_collection);
                            $key_name = Arr::first($collection_keys);
                            $value_name = Arr::first($collection_values);

                        }

                    }

                    $original_resources = call_user_func_array([$this, $original_method], $args);
                    $collection_resources = [];

                    foreach($original_resources as $key => $value) {

                        $collection_resources[] = [
                            $key_name => $key,
                            $value_name => $value
                        ];

                    }

                    $resources[$method] = $collection_resources;

                }

            }

        }

        return response()->json($resources);

    }

    private function getOriginalMethod($method) {

        $snake_case_method = Str::snake($method);
        $snake_case_method_parts = explode('_', $snake_case_method);
        array_pop($snake_case_method_parts);
        $last_key = count($snake_case_method_parts) - 1;
        $snake_case_method_parts[$last_key] = Str::plural($snake_case_method_parts[$last_key]);
        return Str::camel(implode('_', $snake_case_method_parts));

    }

}
