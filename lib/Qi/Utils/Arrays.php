<?php
namespace Qi\Utils;

class Arrays
{
    /**
     * Same as array_combine, but make sure that $values has the same length as $keys.
     * @static
     * @param $keys
     * @param $values
     */
    public static function combine($keys, $values)
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
}
