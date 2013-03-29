<?php
namespace Pfbc\Element;
use Pfbc\Validation\Email as EmailValidation;

class Email extends Textbox
{
    protected $attributes = array('type' => 'email');

    public function render()
    {
        $this->validation[] = new EmailValidation();
        parent::render();
    }
}
