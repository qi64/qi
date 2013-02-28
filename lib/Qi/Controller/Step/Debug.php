<?php

namespace Qi\Controller\Step;

class Debug
{
    public function __invoke($env)
    {
        if ( $this->isDebugEnabled() ) {
            $data = clone $env;
            unset($data->template, $data->output, $data->vars['TPL']);
            $env->output .= debug($data);
        }
    }

    protected function isDebugEnabled()
    {
        return isset( $_REQUEST['_debug'] );
    }
}
