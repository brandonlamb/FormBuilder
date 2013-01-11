<?php
namespace Pfbc;

abstract class AbstractView extends AbstractBase
{
    protected $form;

    /**
     * Constructor
     * @param array $properties
     */
    public function __construct(array $properties = null)
    {
        $this->configure($properties);
    }

    /**
     * jQuery is used to apply css entries to the last element.
     */
    public function jQueryDocumentReady() {}

    public function render() {}

    public function renderCSS()
    {
        echo 'label span.required { color: #B94A48; }';
        echo 'span.help-inline, span.help-block { color: #888; font-size: .9em; font-style: italic; }';
    }

    protected function renderDescriptions($element)
    {
        $shortDesc = $element->getShortDesc();
        if (!empty($shortDesc)) {
            echo '<span class="help-inline">', $shortDesc, '</span>';;
        }

        $longDesc = $element->getLongDesc();
        if (!empty($longDesc)) {
            echo '<span class="help-block">', $longDesc, '</span>';;
        }
    }

    public function renderJS() {}

    /**
     * Render a label
     * @param Element $element
     */
    protected function renderLabel(AbstractElement $element) {}
}
