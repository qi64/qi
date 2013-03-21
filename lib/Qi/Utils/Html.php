<?php

namespace Qi\Utils;
use DOMDocument,
    Qi\Ex\ExTplMissing,
    Qi\Ex\ExRender,
    Qi\Utils\Error;
use Qi\Html\ISafe;

class Html
{
    // @TODO don't pretty print like Xml does
    public static function pp($html)
    {
        $doc = new DOMDocument();
        $doc->strictErrorChecking = false;
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $doc->loadHTML($html);
        return $doc->saveHTML();
    }

    /**
     * @TODO too much responsible
     * @param $file
     * @param array $vars
     * @param int $disable_error
     * @param null $callable
     * @return null|string
     * @throws \Qi\Ex\ExRender
     */
    public static function renderFile($file, $vars = array(), $disable_error = E_NOTICE, $callable = null)
    {
        if ($callable === null && ! is_callable($file) && ! stream_resolve_include_path($file) ) {
            return null;
        }

        ob_start();

        try {
            if ($disable_error) Error::disable($disable_error);

            if ( is_callable($callable) ) {
                $callable($file, $vars);
            }elseif ( is_callable($file) ) {
                $file($vars);
            }elseif ( is_callable($vars) ) {
                $vars($file);
            }else{
                self::includeFile($file, $vars);
            }

            if ($disable_error) Error::pop();
            return ob_get_clean();

        }catch (\Exception $e) {
            if ($disable_error) Error::pop();
            $ex = new ExRender("error on rendering '$file': ".$e->getMessage(), 0, $e);
            $ex->file = $file;
            $ex->vars = $vars;
            $ex->output = ob_get_clean();
            throw $ex;
        }
    }

    protected static function includeFile($__FILE__, $__VARS__ = array())
    {
        extract((array)$__VARS__);
        include $__FILE__;
    }

    public static function p($s)
    {
        if ($s instanceof ISafe) return (string)$s;
        return htmlspecialchars((string)$s);
    }

    public static function googleAnalytics($code)
    {
        return <<<S
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '$code']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
S;

    }
}
