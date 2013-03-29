<?php
namespace Pfbc\Element;
use Pfbc\Validation\Date;

class jQueryUIDate extends Textbox
{
    protected $attributes = array(
        'type' => 'text',
        'autocomplete' => 'off'
    );

    public function render()
    {
        $this->validation[] = new Date();
        parent::render();
    }
}
