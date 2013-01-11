<?php
namespace Pfbc;

abstract class AbstractErrorView extends AbstractBase
{
    protected $form;

    abstract public function applyAjaxErrorResponse();
    abstract public function render();
    abstract public function renderAjaxErrorResponse();

    /**
     * Constructor
     * @param array $properties
     */
    public function __construct(array $properties = null)
    {
        $this->configure($properties);
    }

    /**
     * Print a jquery statement to clear all alert-error classes
     * @return string
     */
    public function clear()
    {
        echo 'jQuery("#', $this->form->getAttribute("id"), ' .alert-error").remove();';
    }

    public function renderCSS() {}

    /**
     * Sets the form object
     * @param Form $form
     * @return $this
     */
    public function setForm(Form $form)
    {
        $this->form = $form;
        return $this;
    }
}
