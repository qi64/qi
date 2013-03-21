<?php


namespace Qi\Html;


class ControlGroup
{
    public static $template = <<<S
<div class="control-group">
    <label class="control-label">%s</label>
    <div class="controls">
        %s
        <span class="help-block">%s</span>
    </div>
</div>

S;

    protected $_template;

    public $label;
    public $help;
    public $control;

    public function setTemplate($template)
    {
        $this->_template = $template;
    }

    public function getTemplate()
    {
        return $this->_template ?: static::$template;
    }

    public function __toString()
    {
        return sprintf($this->getTemplate(), $this->label, $this->control, $this->help);
    }
}
