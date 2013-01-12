<?php
namespace Pfbc\Element;

class Checksort extends Sort
{
    /**
     * @var array
     */
    protected $attributes = array('type' => 'checkbox');

    /**
     * @var string
     */
    protected $inline;

    public function render()
    {
        if (isset($this->attributes['value'])) {
            if (!is_array($this->attributes['value'])) {
                $this->attributes['value'] = array($this->attributes['value']);
            }
        } else {
            $this->attributes['value'] = array();
        }

        if (substr($this->attributes['name'], -2) != '[]') {
            $this->attributes['name'] .= '[]';
        }

        $labelClass = $this->attributes['type'];
        if (!empty($this->inline)) {
            $labelClass .= ' inline';
        }

        $count = 0;
        $existing = '';

        foreach ($this->options as $value => $text) {
            $value = $this->getOptionValue($value);
            if (!empty($this->inline) && $count > 0) {
                echo ' ';
            }

            echo '<label class="', $labelClass, '"><input id="', $this->attributes['id'], '-', $count, '"', $this->getAttributes(array('id', 'value', 'checked', 'name', 'onclick', 'required')), ' value="', $this->filter($value), '"';

            if (in_array($value, $this->attributes['value'])) {
                echo ' checked="checked"';
            }

            echo ' onclick="updateChecksort(this, \'', str_replace(array('"', "'"), array('&quot;', "\'"), $text), '\');"/>', $text, '</label>';

            if (in_array($value, $this->attributes['value'])) {
                $existing .= '<li id="' . $this->attributes['id'] . '-sort-' . $count . '" class="ui-state-default"><input type="hidden" name="' . $this->attributes['name'] . '" value="' . $value . '"/>' . $text . '</li>';
            }

            ++$count;
        }

        echo '<ul id="', $this->attributes['id'], '">', $existing, '</ul>';
    }
}
