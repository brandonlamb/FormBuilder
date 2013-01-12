<?php
namespace Pfbc\Element;
use Pfbc\AbstractOptionElement;

class Sort extends AbstractOptionElement
{
    public function render()
    {
        if (substr($this->attributes['name'], -2) != '[]') {
            $this->attributes['name'] .= '[]';
        }

        echo '<ul id="', $this->attributes['id'], '">';
        foreach ($this->options as $value => $text) {
            $value = $this->getOptionValue($value);
            echo '<li class="ui-state-default"><input type="hidden" name="', $this->attributes['name'], '" value="', $value, '"/>', $text, '</li>';
        }
        echo '</ul>';
    }
}
