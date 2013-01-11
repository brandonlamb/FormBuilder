<?php
namespace Pfbc\Element;

class Email extends Textbox
{
    protected $attributes = array("type" => "email");

    public function render()
    {
        $this->validation[] = new \Pfbc\Validation\Email;
        parent::render();
    }
}
