<?php

namespace App\Helpers;

/**
 * Class ArrayHelper
 * @package App\Helpers
 */
class ArrayHelper
{
    /**
     * @param array $input
     * @return array
     */
    public static function convertKeysToSnakeCase(array $input): array
    {
        $arr = [];
        foreach ($input as $key => $value) {
            $arr[snake_case($key)] = $value;
        }

        return $arr;
    }

    /**
     * @param array $input
     * @return array
     */
    public static function convertKeysToCamelCase(array $input): array
    {
        $arr = [];
        foreach ($input as $key => $value) {
            $arr[camel_case($key)] = $value;
        }

        return $arr;
    }
}
