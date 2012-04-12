<?php

namespace Qi\Tokenizer;

class Tokenizer
{
    public static function minify($source)
    {
        $it = new StandardizeIterator($source);
        $src = '';
        foreach($it as $token) {
            list($code, $content, $line, $name) = array_values($token);
            if ($code == T_DOC_COMMENT || $code == T_COMMENT) continue;
            if ($code == T_WHITESPACE) $content = ' ';
            $src .= $content;
        }
        return $src;
    }

    public static function html($source)
    {
        $it = new StandardizeIterator($source);
        $html = '';
        foreach($it as $token) {
            $html .= self::token2tag($token);
        }
        return $html;
    }

    public static function token2tag($token)
    {
        list($code, $content, $line, $name) = array_values($token);
        $content = htmlspecialchars($content);
        return <<<TAG
<span class="$name">$content</span>
TAG;
    }
}
/* PHP KEYWORDS
abstract class implements interface namespace extends final function static
declare enddeclare const default
array
as continue break
for endfor
foreach endforeach
if endif else elseif
do while endwhile
throw try catch
switch case endswitch
clone global goto instanceof new use var
xor or
private protected public
__CLASS__ __DIR__ __FILE__ __LINE__ __FUNCTION__ __METHOD__ __NAMESPACE__
include include_once require require_once
isset list return print unset __halt_compiler die echo empty exit eval
*/
