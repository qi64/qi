<?php

namespace Qi\View;
use DomainException,
    Qi\Utils\Html;


class Tpl
{
    public function __invoke($__FILE__ = null, $__VARS__ = array())
    {
        if ( ! stream_resolve_include_path($__FILE__) ) {
            return null;
        }
        extract((array)$__VARS__);
        include $__FILE__;
    }
    /*
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
        if ( ! stream_resolve_include_path($this->getFile()) ) {
            return null;
        }

        ob_start();

        try {
            if ($this->disable_error) Error::disable($this->disable_error);
            $this->includeFile();
            if ($this->disable_error) Error::pop();
            return ob_get_clean();

        }catch (\Exception $e) {
            if ($this->disable_error) Error::pop();
            $ex = new ExRender("error on rendering '$__FILE__': ".$e->getMessage(), 0, $e);
            $ex->file = $this->getFile();
            $ex->vars = $this->vars;
            $ex->output = ob_get_clean();
            throw $ex;
        }
    }

    protected function includeFile()
    {
        extract($this->getLocalVars());
        include $this->getFile();
    }
    */
}
