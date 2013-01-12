<?php
namespace Pfbc\Element;
use Pfbc\Validation\Email;

class Email extends Textbox
{
    protected $attributes = array('type' => 'email');

    public function render()
    {
        $this->validation[] = new Email();
        parent::render();
    }
}
