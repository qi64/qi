<?php

namespace Qi\Gdata;
use Qi\Http\Curl,
    Qi\Http\CurlStream,
    DomainException;

class ClientLogin
{
    const URL = 'https://www.google.com/accounts/ClientLogin';
    const TIMEOUT = 3600;
    public $accountType = 'HOSTED_OR_GOOGLE';
    public $Email = '';
    public $Passwd = '';
    public $source = 'company-appname-v1';
    public $service = '';

    public function login($force = false)
    {
        if (!$force) {
            $data = $this->getData();
            $auth = $this->getAuthFromSession();
            if ($auth) return $auth;            
        }
        $response = $this->post(self::URL, $data);
        $auth = $this->parseResponse($response);
        $this->setAuthToSession($auth);
        return $auth;
    }

    protected function getAuthFromSession()
    {
        @session_start();
        $auth = @$_SESSION[$this->hash()];
        return ($auth && (@$_SESSION[$this->hash().'_created_at'] + self::TIMEOUT) >= time())
               ? $auth : null;
    }

    protected function setAuthToSession($auth)
    {
        $_SESSION[$this->hash()] = $auth;
        $_SESSION[$this->hash().'_created_at'] = time();
    }

    protected function hash()
    {
        $hash = md5(var_export($this->getData(), true));
        return get_called_class()."_$hash";
    }

    protected function post($url, $data)
    {
        $curl = new Curl($url);
        return $curl->post($data);
    }

    protected function getData()
    {
        return get_object_vars($this);
    }

    protected function parseResponse($response)
    {
        $this->handleError($response);
        list($foo, $auth) = explode('Auth=', $response);
        return trim($auth);
    }

    protected function handleError($response)
    {
        $matches = array();
        if ( preg_match('!Error=(.+)!', $response, $matches) ) {
            throw new DomainException($matches[1]);
        }
    }
}
