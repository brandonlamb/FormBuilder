<?php
namespace Pfbc\Element;
use Pfbc\Validation\Numeric;

class Number extends Textbox
{
    protected $attributes = array('type' => 'number');

    public function render()
    {
        $this->validation[] = new Numeric();
        parent::render();
    }
}
