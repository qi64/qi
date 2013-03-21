<?php

namespace Qi\Utils;

/**
 * mb_internal_encoding('UTF-8');
 */
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
            $accents    = str_split('çáàãâéèêíìîóòõôúùûÇÁÀÃÂÉÈÊÍÌÎÓÒÕÔÚÙÛ', 2);//each char is 2 bytes length.
            $normalized = str_split('caaaaeeeiiioooouuuCAAAAEEEIIIOOOOUUU');
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

    /**
     * Convert CamelCasedWord to Camel_Cased_Word like class names. (uncamelize)
     * @static
     * @param $camel_cased_word
     */
    public static function underscore($camel_cased_word)
    {
        $replace = array(
            '!([A-Z\d])([A-Z][a-z])!' => '$1_$2', // PHPAcronym to PHP_Acronym
            '!([a-z\d])([A-Z])!' => '$1_$2' // CamelCasedWord to Camel_Cased_Word
        );
        return preg_replace(array_keys($replace), $replace, $camel_cased_word);
    }

    /**
     * Convert MyTableName or My Table Name to my_table_name
     * @static
     * @param $s
     * @return mixed|string
     */
    public static function tableize($s)
    {
        return self::slugize(self::underscore($s), '_');
    }

    public static function classify($s)
    {
        $s = preg_replace('!_|-!', ' ', $s);
        $s = ucwords($s);
        $s = preg_replace('!\s+!', '', $s);
        return $s;
    }

    public static function titleize($s)
    {
        $s = preg_replace('!_|-!', ' ', $s);
        $s = ucwords($s);
        return $s;
    }
}
