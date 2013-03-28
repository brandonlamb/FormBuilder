<?php
namespace Pfbc\Element;

use Pfbc\AbstractElement;

class Button extends AbstractElement
{
    /**
     * @var array
     */
    protected $attributes = array('type' => 'submit', 'value' => 'Submit');

    /**
     * Constructor
     * {@inherit}
     */
    public function __construct($label = 'Submit', $type = '', array $properties = null)
    {
        if (!is_array($properties)) {
            $properties = array();
        }

        if (!empty($type)) {
            $properties['type'] = $type;
        }

        $class = 'btn';
        if (empty($type) || $type == 'submit') {
            $class .= ' btn-primary';
        }

        if (!empty($properties['class'])) {
            $properties['class'] .= ' ' . $class;
        } else {
            $properties['class'] = $class;
        }

        if (empty($properties['value'])) {
            $properties['value'] = $label;
        }

        parent::__construct('', '', $properties);
    }
}
