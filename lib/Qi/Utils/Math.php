<?php

namespace Qi\Utils;

class Math
{
    public static function humanizeBytes($b, $f = '%0.1f')
    {
        $K = 1024;
        $M = $K * 1024;
        $G = $M * 1024;
        if ($b >= $G) {
            $b /= $G;
            $f .= ' GB';
        }elseif ($b >= $M) {
            $b /= $M;
            $f .= ' MB';
        }elseif ($b >= $K) {
            $b /= $K;
            $f .= ' KB';
        }else{
            $f.= ' bytes';
        }
        return sprintf($f, $b);
    }
}
