<?php
namespace Pfbc;

interface ViewInterface
{
    /**
     * Render the form
     * @return string
     */
    public function render();

    /**
     * Render the element label
     * @return string
     */
    public function renderLabel(AbstractElement $element);
}
