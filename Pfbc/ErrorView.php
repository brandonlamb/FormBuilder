<?php
namespace PFBC;

abstract class ErrorView extends Base
{
    protected $_form;

    public function __construct(array $properties = null)
    {
        $this->configure($properties);
    }

    abstract public function applyAjaxErrorResponse();

    public function clear()
    {
        echo 'jQuery("#', $this->_form->getAttribute("id"), ' .alert-error").remove();';
    }

    abstract public function render();
    abstract public function renderAjaxErrorResponse();

    public function renderCSS() {}

    public function _setForm(Form $form)
    {
        $this->_form = $form;
    }
}
