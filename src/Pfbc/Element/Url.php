<?php
namespace Pfbc\Element;
use Pfbc\Validation\Url;

class Url extends Textbox
{
    protected $attributes = array('type' => 'url');

    public function render()
    {
        $this->validation[] = new Url();
        parent::render();
    }
}
