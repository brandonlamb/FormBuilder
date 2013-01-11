<?php
namespace Pfbc\Element;

class Url extends Textbox
{
    protected $attributes = array("type" => "url");

    public function render()
    {
        $this->validation[] = new \Pfbc\Validation\Url;
        parent::render();
    }
}
