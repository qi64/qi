<?php

function csv2dict($url)
{
  $lines = file($url);
  $header = str_getcsv(array_shift($lines));
  $header = array_map('strtolower', $header);
  $rows = array();
  $header_length = count($header);
  foreach($lines as $line):
    $dados = array();
    $dados = str_getcsv($line);
    $dados = array_pad($dados, $header_length, null);
    $row = array_combine($header, $dados);
    $rows[] = $row;
  endforeach;
  return $rows;
}

function download_gsheet($key, $target, $cache = 600)
{
  $f = "https://docs.google.com/spreadsheet/pub?key=$key&single=true&gid=0&output=csv";
  $t = file_exists($target) ? filemtime($target) : 0;
  if (time() - $t < $cache) return null;
  $csv = file_get_contents($f);
  file_put_contents($target, $csv);
  return $csv;
}

// curl -d "accountType=HOSTED_OR_GOOGLE&Email=img@qi64.com&Passwd=123qweasdzxc&source=phpicasa&service=lh2" https://www.google.com/accounts/ClientLogin
function googleAuth()
{
    $curl = new Curl('https://www.google.com/accounts/ClientLogin');
    $post['accountType'] = 'HOSTED_OR_GOOGLE';
    $post['Email'] = 'img@qi64.com';
    $post['Passwd'] = '123qweasdzxc';
    $post['source'] = 'appengine-picasa';
    $post['service'] = 'lh2';
    $response = $curl->post($post);
    list($foo, $auth) = explode('Auth=', $response);
    return trim($auth);
}

// curl -X POST --data-binary "@foto.jpg" -H "Content-Type: image/jpeg" -H "GData-Version: 2" -H "Authorization: GoogleLogin auth=$AUTH" https://picasaweb.google.com/data/feed/api/user/default/albumid/default
function upload2picasa($file) {
    $auth = 'DQAAAO8AAADtoVHBXMkFt9R-GM7CJKf7b62f7LvhAvwT9fSDay0vARqnB9qmdSg_cy99vB2teiuSNHuH9ko-fCwSQlBGdBcSqXGhXeGqhNfIcY6yFCIOpU6Yuz4slmyKcLqvw_bhB5kKGQ0wFtkTRgOOwksZmDI7DQO-fZyVM7zkwI9L-WMzHYCOxJyem0HyKP5S_VwXvwRTTl03l2M5fjHzPubSSacZwvfrPCldAFfpTJqBl0Cg_x6VVzHDd1Ft88YkIi0nm3GkPAYwQbhfvBqA4xBx4UTw4_q7AZep6eFtkTvha66PLpCe6UKbGdwRVOoxtsAeXjE';
    $auth = 'DQAAAO4AAAD-_WtXx16BKfzXhi2wBbbKOMVOYF0kcpdeRiTO21CFciBmBDdREN4FSeh7O5QXOltiA6mKYaxExH4Fd6ATEHkT58DiZ47JgVHGlrT7fMitJTRTA0w-CNsPIKcRLAklGBUeVNohOVri1bEquJIArq811aFEwS15CjS1iZZaNj8a4YaFj0Ozl1q3Zw3LaR60_LuUHvfXIxSZm43K88yyuAKFztM7GDM9BZZKGfKzpayrWY7F1dFqBL8MRHtxrIqISmBfxcvtcueJK3A5kmoS319kXToiorrcPLQkeDypmLDbLCYn63-Yf0UoYeaqsrSuF38';
    $auth = @$_SESSION['google-token'] ?: googleAuth();
    $_SESSION['google-token'] = $auth;
    $curl = new Curl("https://picasaweb.google.com/data/feed/api/user/default/albumid/default");
    $content = file_get_contents($file);
    $curl->headers = array(
        'GData-Version' => 2,
        'Authorization' => "GoogleLogin auth=$auth",
        'Slug' => 'img',
        'Content-Type' => 'image/jpeg',
    );
    $xml = $curl->post($content);
    if ( ! $curl->isOk() ) {
        if ( $curl->response_code == 403 && $xml == "Token expired" ) {
            unset($_SESSION['google-token']);
            return upload2picasa($file);
        }else{
            var_dump($curl->response_code);
            var_dump($xml);
        }
    }
    $sxml = simplexml_load_string($xml);
    $ns = $sxml->getDocNamespaces();
    $url = (string)$sxml->children($ns['media'])->group->content->attributes()->url;
    $url = substr($url, 0, strrpos($url, '/') + 1 );
    return 'http' . strstr($url, ':');
}
