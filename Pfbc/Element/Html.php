<?php
namespace Pfbc\Element;
use Pfbc\AbstractElement;

class Html extends AbstractElement
{
    public function __construct($value)
    {
        $properties = array('value' => $value);
        parent::__construct('', '', $properties);
    }

    public function render()
    {
        echo $this->attributes['value'];
    }
}
