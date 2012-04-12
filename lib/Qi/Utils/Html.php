<?php

namespace Qi\Utils;
use DOMDocument;

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

    public static function google_analytics($code)
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
