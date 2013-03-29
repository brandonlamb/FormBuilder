<?php
namespace Pfbc;

abstract class AbstractView extends AbstractBase
{
    /** @var string */
    protected $class;

    /**
     * Constructor
     * @param array $properties
     */
    public function __construct(array $properties = null)
    {
        $this->configure($properties);
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
}
