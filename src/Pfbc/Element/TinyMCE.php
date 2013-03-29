<?php
namespace Pfbc\Element;

class TinyMCE extends Textarea
{
    protected $basic;

    public function render()
    {
        echo '<textarea', $this->getAttributes(array('value', 'required')), '>';
        if (!empty($this->attributes['value'])) {
            echo $this->attributes['value'];
        }
        echo '</textarea>';
    }
}
