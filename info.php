<?php
/* https://github.com/GaretJax/phpbrowscap/blob/master/src/phpbrowscap/Browscap.php
@todo permitir manipular a sessão e os cookies
@todo javascript para exibir apenas os valores modificados do ini
@todo diferenciar os valores que são diferentes do padrao do php
@todo over nas linhas da tabela
@todo ao invés de 1 e 0, exibir um check e um X
@todo adicionar tab para conectar no MySql (futuramente outros bancos)
@todo listar os diretórios raiz e mostrar a permissão de escrita
*/
function h($s) { return @htmlspecialchars($s); }
function access($bm) {
    if ($bm == 7) return 'PHP_INI_ALL';
    if ($bm == 4) return 'PHP_INI_SYSTEM';
    if ($bm == 6) return 'PHP_INI_PERDIR';
}
function changed($values) {
  return $values['global_value'] == $values['local_value'] ? '' : ' changed';
}
function array_index($array, $index)
{
    return $array[$index];
}
function user_name($id)
{
    return array_index(posix_getpwuid($id), 'name');
}
function group_name($id)
{
    return array_index(posix_getgrgid($id), 'name');
}
function phpinfo2a()
{
    ob_start();
    phpinfo(INFO_GENERAL);
    $phpinfo = ob_get_clean();
    $doc = new DOMDocument();
    $doc->strictErrorChecking = false;
    $doc->loadHTML($phpinfo);
    $sxml = simplexml_import_dom($doc);
    $keys = $sxml->xpath('//td[@class="e"]');
    foreach($keys as &$k) $k = trim($k);
    $values = $sxml->xpath('//td[@class="v"]');
    foreach($values as &$v) $v = trim($v);
    return array_combine($keys, $values);
    //$doc = dom_import_simplexml($sxml);
    //echo $doc->ownerDocument->saveHTML();
    //libxml_use_internal_errors(true);
    //$sxml = simplexml_load_string($phpinfo);
    //echo $sxml->asXML();
}

function humanizeBytes($b, $f = "%0.1f ")
{
    $K = 1024;
    $M = $K * 1024;
    $G = $M * 1024;
    if ($b >= $G) {
        $b /= $G;
        $f .= 'GB';
    }elseif ($b >= $M) {
        $b /= $M;
        $f .= 'MB';
    }elseif ($b >= $K) {
        $b /= $K;
        $f .= 'KB';
    }else{
        $f.= 'bytes';
    }
    return sprintf($f, $b);
}

session_start();

$user = 'php';
$passwd = 'info';

if ($user && !isset($_SERVER['PHP_AUTH_USER']) 
          || $_SERVER['PHP_AUTH_USER'] != $user 
          || $_SERVER['PHP_AUTH_PW'] != $passwd) {
    header("WWW-Authenticate: Basic realm='Secure Area'");
    header("$_SERVER[SERVER_PROTOCOL] 401 Unauthorized");
    die("Inform user and password.");
}

$sys = array(
    'php_uname' => php_uname(),
    'phpversion' => phpversion(),
    'php_sapi_name' => php_sapi_name(),
    'PHP_OS' => PHP_OS,
    'get_current_user' => get_current_user(),
    'getmypid' => getmypid(),
    'getmyuid' => user_name(getmyuid()),
    'getmygid' => group_name(getmygid()),
    'getmyinode' => getmyinode(),
    'getcwd' => getcwd(),
    'disk_free_space' => humanizeBytes(disk_free_space('.')),
    'disk_total_space' => humanizeBytes(disk_total_space('.')),
    'fileowner' => user_name(fileowner('.')),
);

$server = $_SERVER; ksort($server);
$env = $_ENV; ksort($env);
$cookies = $_COOKIE; ksort($cookies);
$session = $_SESSION; ksort($session);
$extensions = get_loaded_extensions();
natcasesort($extensions);
$extensions = array_values($extensions);
$files = $_FILES;
$ini = ini_get_all();
$apache = function_exists('apache_get_modules') ? apache_get_modules() : array();
$phpinfo = phpinfo2a();
$infos = compact('sys', 'phpinfo', 'server', 'env', 'cookies', 'session', 'extensions', 'apache', 'ini', 'files');

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
    <title>PHP INFO</title>
    <link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.min.css" />
    <style>
        #main {width: 1000px; margin: 20px auto;}
        .table {width: auto; margin: auto;}
        .table tr:nth-child(even) {background-color: #fafafa;}
        td {white-space: nowrap;}
        td.value {word-break: break-all; white-space: normal; }
        .table tr.changed {background-color: orange; color: white; font-weight: bold}
        .PHP_INI_ALL {color: green;}
        .PHP_INI_SYSTEM {color: darkred;}
        .PHP_INI_PERDIR {color: #ff8c00;}
        #phpinfo th {
            white-space: nowrap;
            vertical-align: middle;
            text-align: right;
        }
        #phpinfo td {
            white-space: normal;
        }
    </style>
</head>

<body><div id="main">
    <ul class="nav nav-tabs">
    <? foreach($infos as $label => $info): ?>
        <li>
            <a href="#<?=h($label)?>" data-toggle="tab">
                <?=h($label)?> (<?=count($info)?>)
            </a>
        </li>
    <? endforeach ?>
    </ul>

<? unset($infos['ini'], $infos['files']) ?>

    <div class="tab-content">

        <? foreach($infos as $label => $info): ?>
        <div class="tab-pane" id="<?=h( $label )?>">
            <table class="table table-bordered table-condensed">
            <? foreach($info as $k => $v): ?>
                <tr>
                    <th><?=h( $k )?></th>
                    <td><?=h( $v )?></td>
                </tr>
            <? endforeach ?>
            </table>
        </div>
        <? endforeach ?>

        <div class="tab-pane" id="ini">
            <table class="table table-bordered table-condensed">
                <thead>
                    <tr>
                        <th>access</th>
                        <th>cfg</th>
                        <th>local value (<a href="http://php.net/manual/ini.list.php" target="_blank">reference</a>)</th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($ini as $cfg => $values): ?>
                    <tr class="<?=access($values['access'])?><?= changed($values) ?>">
                        <td><?=access($values['access'])?></td>
                        <td><?=h( $cfg )?></td>
                        <!-- <td class="value"><?=h( $values['global_value'] )?></td> -->
                        <td class="value"><?=h( $values['local_value'] )?></td>
                    </tr>
                    <? endforeach ?>
                </tbody>
            </table>
        </div><!-- #ini.tab-pane -->

        <div class="tab-pane" id="files">
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="file[]" multiple="multiple">
                <input class="btn" type="submit">
            </form>

<pre>
<? if ($files) print_r($files) ?>
</pre>
        </div><!-- #files.tab-pane -->
    </div><!-- .tab-content -->

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.0.1/bootstrap.min.js"></script>
    <script>
        jQuery(function($) {
          var hash = window.location.hash
          if (hash) {
            $(hash).addClass('active')
            $('a[href=' + hash + ']').parent().addClass('active')
          }
          $(".nav-tabs a").click(function() {
            window.location.hash = this.hash
          })
        })
    </script>
</div></body>
</html>
