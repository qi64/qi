<?php

namespace Qi\Utils;


class Inflector
{
    protected static $normalize_translation;

    /**
     * Initialize $normalize_translation
     * @static
     */
    protected static function getNormalizeTranslation()
    {
        if (!self::$normalize_translation) {
            $accents    = str_split('áàãâéèêíìîóòõôúùûÁÀÃÂÉÈÊÍÌÎÓÒÕÔÚÙÛ', 2);//each char is 2 bytes length.
            $normalized = str_split('aaaaeeeiiioooouuuAAAAEEEIIIOOOOUUU');
            self::$normalize_translation = array_combine($accents, $normalized);
        }
        return self::$normalize_translation;
    }

    public static function slugize($s, $char = '-')
    {
        $s = self::normalize($s);
        $s = self::lower($s);
        $s = self::dasherize($s, $char);
        return $s;
    }

    /**
     * Convert accents chars to the non accent version
     * @static
     * @param $s
     */
    public static function normalize($s)
    {
        return strtr($s, self::getNormalizeTranslation());
    }

    public static function lower($s)
    {
        return mb_strtolower($s, 'UTF-8');
    }

    /**
     * Convert anything but alphanuns to dash
     * @static
     * @param $s Subject String
     * @params $char Custom char
     * @return mixed|string
     */
    public static function dasherize($s, $char = '-')
    {
        $s = preg_replace('/[^a-zA-Z0-9\x{00C0}-\x{00FF}]+/u', $char, $s);
        $s = trim($s, $char);
        return $s;
    }
}
