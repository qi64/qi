<?php

namespace Qi\Http;

/**
 * @todo dispatch event login/logout, to easy logging https://github.com/fabpot/event-dispatcher
 */
class Auth
{
    protected $sessionKey;
    protected $data = array();

    public function __construct($sessionKey = null)
    {
        if (!isset($_SESSION)) session_start();
        if (!$sessionKey) $sessionKey = get_called_class();
        $this->sessionKey = $sessionKey;
        if ($this->isLogged())
            $this->data = $_SESSION[$this->getSessionKey()];
    }

    public function getSessionKey()
    {
        return $this->sessionKey;
    }

    public function isLogged()
    {
        return isset($_SESSION[$this->getSessionKey()]);
    }

    public function isAdmin()
    {
        return (bool)@$this->data['admin'];
    }

    public function login($data = array())
    {
        if ($data) $this->data = $data;
        $_SESSION[$this->getSessionKey()] = $this->getData();
    }

    public function logout()
    {
        unset($_SESSION[$this->getSessionKey()]);
    }

    public function isAnonymous()
    {
        return ! $this->isLogged();
    }

    public function getData()
    {
        return $this->data;
    }
}
