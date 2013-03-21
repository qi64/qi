<?php

namespace Qi\Utils;

class Arrays
{
    public static function map($array, $f)
    {
        if ( is_string($f) ) {
            $f = function($v, $k) use ($f) {
                return sprintf($f, $k, $v);
            };
        }
        // return array_map($f, array_values($array), array_keys($array));
        $result = array();
        foreach($array as $k => $v) $result[$k] = $f($v, $k);
        return $result;
    }

    /**
     * fix PHP's array_merge_recursive, that duplicates keys
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public static function merge($array1, $array2)
    {
        $array2 = is_array($array2) ? $array2 : array();
        $merged = is_array($array1) ? $array1 : array();

        foreach ( $array2 as $key => &$value )
            if ( is_array($value) && isset($merged[$key]) && is_array($merged[$key]) )
                $merged[$key] = static::merge($merged[$key], $value);
            else
                $merged[$key] = $value;

        return $merged;
    }

    /**
     * Merge 2 arrays taking the values from b but only the keys from a.
     * @static
     * @param array $a
     * @param array $b
     * @return array
     */
    public static function mergeIntersect(array $a, array $b)
    {
        return array_merge($a, array_intersect_key($b, $a));
    }

    /**
     * Same as array_combine, but make sure that $values has the same length as $keys.
     * @static
     * @param $keys
     * @param $values
     */
    public static function combine(array $keys, array $values)
    {
        $kc = count($keys);
        $vc = count($values);
        if ($kc != $vc) {
            if ($kc > $vc) {
                $values = array_pad($values, $kc, null);
            }else{
                $values = array_slice($values, 0, $kc);
            }
        }
        return array_combine($keys, $values);
    }

    public static function indexBy($array, $key)
    {
        $result = array();
        foreach($array as $row) {
            $result[$row[$key]] = $row;
        }
        return $result;
    }

    /**
     * Same as indexBy, but accumulate in array
     * @static
     * @param $array
     * @param $key
     * @return array
     */
    public static function groupBy($array, $key)
    {
        $result = array();
        foreach($array as $row) {
            $k = $row[$key];
            if ( ! isset($result[$k]) ) {
                $result[$k] = array();
            }
            $result[$k][] = $row;
        }
        return $result;
    }

    /**
     * Ungroup this:
     * [
     *      'label' => ['a', 'b'],
     *      'value' => [1  ,  2 ]
     * ]
     * into this:
     * [
     *      ['label' => 'a', 'value' => 1],
     *      ['label' => 'b', value => '2']
     * ]
     */
    public static function ungroup($array)
    {
        if (!$array) return array(); // accept falsy as an empty array
        $result = array();
        //$item_default = array_fill_keys(array_keys($array), null);
        foreach($array as $k => $values) {
            foreach($values as $i => $value) {
                $result[$i][$k] = $value;
            }
        }
        return $result;
    }

    /**
     * Reverse above method
     */
    public static function group($array)
    {
        if (!$array) return array(); // accept falsy as an empty array
        $result = array();
        //$item_default = array_fill_keys(array_keys($array), null);
        foreach($array as $i => $record) {
            foreach($record as $k => $v) {
                $result[$k][$i] = $v;
            }
        }
        return $result;
    }

    public static function groupCount($array)
    {
        $group = array();
        foreach($array as $item) {
            @$group[$item]++;
        }
        return $group;
    }

    /**
     * Returns only local vars from get_defined_vars
     * @usage Arrays::removeGlobals(get_defined_vars());
     * @static
     * @param $array
     * @return array
     */
    public static function removeGlobals(array $array)
    {
        return @array_diff( $array, array(array()) );
    }

    /**
     * Converts array(1,2,array(3,4, array(5,6,7), 8), 9); into:
     *          array(1,2,      3,4,       5,6,7,  8,  9);
     * @static
     * @param $array
     * @return array
     */
    public static function flatten(array $array) {
        $flat = array();
        array_walk_recursive($array, function ($v, $k) use (&$flat) {
            is_numeric($k) ? $flat[] = $v : $flat[$k] = $v;
        });
        return $flat;
    }

    /**
     * Convert array('name', 'john', 'age', 23) into array('name' => 'john', 'age' => 23)
     * @param array $array
     * @return array
     */
    public static function chunkJoin(array $array)
    {
        $chunks = array_chunk($array, 2);
        $params = array();
        foreach($chunks as $chunk) {
            list($k, $v) = $chunk;
            $params[$k] = $v;
        }
        return $params;
    }
}
