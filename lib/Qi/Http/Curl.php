<?php

namespace Qi\Http;

class Curl
{
    public $method = "GET";
    public $url = "";
    public $headers = array();
    public $body = "";
    public $parsed_response_headers = array();
    public $response_headers = array();
    public $response_body = "";
    public $response_code = 0;

    public function __construct($url = null)
    {
        $this->curl = curl_init($url);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
    }

    public function isOk()
    {
        return $this->response_code >= 200 && $this->response_code < 300;
    }

    public function post($data = array())
    {
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->build_headers());
        return $this->load();
    }

    function build_headers()
    {
        $headers = array();
        foreach($this->headers as $k => $v) $headers[] = trim("$k: $v");
        return $headers;
    }

    public function load()
    {
        $response = curl_exec($this->curl);
        $this->response_body = $response;
        $this->response_code =  curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        return $this->response_body;
    }
}
