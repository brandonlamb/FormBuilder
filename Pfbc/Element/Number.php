<?php
namespace Pfbc\Element;

class Number extends Textbox
{
    protected $attributes = array("type" => "number");

    public function render()
    {
        $this->validation[] = new \Pfbc\Validation\Numeric;
        parent::render();
    }
}
