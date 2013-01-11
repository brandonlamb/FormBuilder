<?php
namespace Pfbc;

abstract class AbstractOptionElement extends AbstractElement
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor
     * @param string $label
     * @param string $name
     * @param array $options
     * @param array $properties
     */
    public function __construct($label, $name, array $options, array $properties = null)
    {
        $this->options = $options;
        if (!empty($this->options) && array_values($this->options) === $this->options) {
            $this->options = array_combine($this->options, $this->options);
        }

        parent::__construct($label, $name, $properties);
    }

    /**
     * Get an option's value
     * @param string $value
     * @return string
     */
    protected function getOptionValue($value)
    {
        $position = strpos($value, ':pfbc');
        if ($position !== false) {
            if ($position == 0) {
                $value = "";
            } else {
                $value = substr($value, 0, $position);
            }
        }

        return $value;
    }
}
