<?php

namespace Qi\Gdata;
use Qi\Http\Curl,
    Qi\Http\CurlStream,
    DomainException;

/**
 * curl -X POST --data-binary "@foto.jpg" -H "Content-Type: image/jpeg" -H "GData-Version: 2" -H "Authorization: GoogleLogin auth=$AUTH"  https://picasaweb.google.com/data/feed/api/user/default/albumid/default
 */

class PicasaUpload
{
    const URL = 'https://picasaweb.google.com/data/feed/api/user/default/albumid/default';
    protected $clientLogin;

    public function __construct(ClientLogin $clientLogin)
    {
        $this->clientLogin = $clientLogin;
    }

    public function upload($img_data, $slug = 'img', $force_auth = false)
    {
        $auth = $this->getAuthToken($force_auth);
        $curl = new Curl(self::URL);
        $curl->headers = array(
            'GData-Version' => 2,
            'Authorization' => "GoogleLogin auth=$auth",
            'Slug' => $slug,
            'Content-Type' => 'image/jpeg',
        );
        $xml = $curl->post($img_data);
        if ( ! $curl->isOk() ) {
            if ( $curl->response_code == 403 && $xml == "Token expired" ) {
                return $this->uplaod($img_data, $slug, true);
            }else{
                throw new DomainException($xml, $curl->response_code);
            }
        }
        return $this->parseXml($xml);
    }

    protected function parseXml($xml)
    {
        $sxml = simplexml_load_string($xml);
        $ns = $sxml->getDocNamespaces();
        $url = (string)$sxml->children($ns['media'])->group->content->attributes()->url;
        $url = substr($url, 0, strrpos($url, '/') + 1 );
        return 'http' . strstr($url, ':');
    }

    protected function getAuthToken($force_auth = false)
    {
        return $this->clientLogin->login($force_auth);
    }
}
