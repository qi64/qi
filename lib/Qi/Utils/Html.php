<?php

namespace Qi\Utils;
use DOMDocument, DomainException;

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

    public static function renderFile($__FILE__, $__VARS__ = array())
    {
        if ( ! stream_resolve_include_path($__FILE__) ) {
            $msg = "included file '$__FILE__' not found.";
            throw new DomainException($msg); // @TODO TplMissingException
        }
        extract($__VARS__);
        ob_start();
        include $__FILE__;
        return ob_get_clean();
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
