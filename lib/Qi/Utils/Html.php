<?php

namespace Qi\Utils;
use DOMDocument,
    Qi\Ex\ExTplMissing,
    Qi\Ex\ExRender,
    Qi\Utils\Error;

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

    public static function renderFile($__FILE__, $__VARS__ = array(), $disable_error = E_NOTICE)
    {
        if ( ! stream_resolve_include_path($__FILE__) ) {
            $msg = "included file '$__FILE__' not found.";
            $ex = new ExTplMissing($msg);
            $ex->file = $__FILE__;
            $ex->vars = $__VARS__;
            throw $ex;
        }

        extract((array)$__VARS__);
        ob_start();

        try {
            if ($disable_error) Error::disable($disable_error);
            include $__FILE__;
            if ($disable_error) Error::pop();
            return ob_get_clean();

        }catch (\Exception $e) {
            if ($disable_error) Error::pop();
            $ex = new ExRender("error on rendering '$__FILE__': ".$e->getMessage(), 0, $e);
            $ex->file = $__FILE__;
            $ex->vars = $__VARS__;
            $ex->output = ob_get_clean();
            throw $ex;
        }
    }

    public static function p($s)
    {
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
