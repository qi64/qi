<?php

namespace Qi\View;
use DomainException,
    Qi\Utils\Html;


class Tpl
{
    public $template = 'layout';
    public $tplMask = '';

    public function getFile()
    {
        $file = $this->buildPath($this->template);
        if ( ! stream_resolve_include_path($file) ) {
            $msg = "included file '$file' not found.";
            throw new DomainException($msg); // @TODO TplMissingException
        }
        return $file;
    }

    public function inc($path, $localVars = array())
    {
        return $this->renderFile($this->buildPath($path), $localVars);
    }

    protected function buildPath($path)
    {
        return sprintf($this->tplMask, $path);
    }

    public function __toString()
    {
        try {
            return (string)$this->render();
        }catch(DomainException $e) {
            return (string)$e;
        }
    }

    public function getVars()
    {
        return array();
    }

    public function renderFile($__FILE__, $__VARS__)
    {
        extract($__VARS__);
        ob_start();
        include $__FILE__;
        return ob_get_clean();
    }

    public function render()
    {
        extract($this->getVars());
        ob_start();
        //$this->pushPath($__FILE__);
        include $this->getFile();
        //$this->popPath();
        return ob_get_clean();
    }

    public function pushPath()
    {

    }
}
