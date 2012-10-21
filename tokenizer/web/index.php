<?php
use Qi\Tokenizer\Tokenizer;
$ROOT = dirname(dirname(__DIR__));
require_once "$ROOT/lib/autoload.php";
$html = Tokenizer::html( file_get_contents("$ROOT/tokenizer/bin/all_tokens.fixture.php") );
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tokenizer</title>
    <link rel="stylesheet" type="text/css" href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css" />
<style>
body {
    cursor: default;
}
#php {
    background-color: white;
    font-size: 12px;
    line-height: 14px;
}
#php span {
    padding: 1px 2px;
}
#php span:hover {
    background-color: gray;
    color: white;
}
#tooltip {
}
.T_OPEN_TAG, .T_CLOSE_TAG, .T_OPEN_TAG_WITH_ECHO {
    color: red;
    font-weight: bold;
}
.T_CONSTANT_ENCAPSED_STRING {
    color: green;
    font-weight: bold;
}
.T_SEMICOLON {
    font-weight: bold;
}
.T_COMMENT, .T_DOC_COMMENT {
    color: silver;
}
.T_USE,.T_TRAIT,.T_NAMESPACE,.T_CLASS,.T_FUNCTION,.T_GOTO,.T_INCLUDE,.T_REQUIRE,.T_INCLUDE_ONCE,.T_REQUIRE_ONCE,
.T_ABSTRACT,.T_EXTENDS,.T_INSTEADOF,.T_IMPLEMENTS,.T_FINAL,.T_INTERFACE,.T_CONST,.T_DOUBLE_COLON,.T_GLOBAL,.T_RETURN {
    color: darkblue;
    font-weight: bold;
}
</style>
</head>
<body>
<div id="tooltip" class="tooltip in right">
    <div class="tooltip-arrow"></div>
    <div class="tooltip-inner">teste</div>
</div>
<pre id="php">
<?= $html ?>
</pre>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.0.1/bootstrap.min.js"></script>
<script>
jQuery(function($) {
    $('#tooltip').hide()
    $("#php").on('mouseover', 'span', function(e) {
        var span = $(this)
        var tip = $('#tooltip')
        tip.show().find('.tooltip-inner').text(span.attr('class'))
        tip.css({
            left: span.offset().left + span.outerWidth(true),
            top: span.offset().top + span.outerHeight(true) / 2 - tip.outerHeight(true) / 2
        })
    }).on('mouseout', 'span', function(e) {
        $('#tooltip').hide()
    });
})
</script>
</body>
</html>
