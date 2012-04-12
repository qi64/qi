<?php

namespace Qi\Utils;

class Arrays
{
    public static function map($array, $f)
    {
        if ( is_string($f) ) {
            $f = function($k, $v) use ($f) {
                return sprintf($f, $k, $v);
            };
        }
        // return array_map($f, array_values($array), array_keys($array));
        $result = array();
        foreach($array as $k => $v) $result[$k] = $f($k, $v);
        return $result;
    }

    /**
     * Merge 2 arrays taking the values from b but only the keys from a.
     * @static
     * @param array $a
     * @param array $b
     * @return array
     */
    public static function merge_intersect(array $a, array $b)
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
}
