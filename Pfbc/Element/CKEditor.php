<?php
namespace Pfbc\Element;

class CKEditor extends Textarea
{
    protected $basic;

    public function render()
    {
        echo '<textarea', $this->getAttributes(array('value', 'required')), '>';
        if (!empty($this->_attributes['value'])) {
            echo $this->_attributes['value'];
        }
        echo '</textarea>';
    }
}
