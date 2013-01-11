<?php
namespace Pfbc\Element;
use Pfbc\AbstractElement;

class Textarea extends AbstractElement
{
    protected $attributes = array("rows" => "5");

    public function render()
    {
        echo "<textarea", $this->getAttributes("value"), ">";
        if (!empty($this->attributes["value"])) {
            echo $this->filter($this->attributes["value"]);
        }
        echo "</textarea>";
    }
}
