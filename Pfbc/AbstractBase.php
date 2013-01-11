<?php
namespace PFBC;

abstract class AbstractBase
{
    /**
     * @var array
     */
    protected $attributes = array();

    /**
     * Set form configuration options
     * @param array $properties
     * @return $this
     */
    public function configure(array $properties = null)
    {
        if (!empty($properties)) {
            $class = get_class($this);

            // The property_reference lookup array is created so that properties can be set case-insensitively.
            $available = array_keys(get_class_vars($class));
            $property_reference = array();
            foreach ($available as $property) {
                $property_reference[strtolower($property)] = $property;
            }

            // The method reference lookup array is created so that "set" methods can be called case-insensitively
            $available = get_class_methods($class);
            $method_reference = array();
            foreach ($available as $method) {
                $method_reference[strtolower($method)] = $method;
            }

            foreach ($properties as $property => $value) {
                $property = strtolower($property);
                // Properties beginning with "_" cannot be set directly.
                if ($property[0] != '_') {
                    // If the appropriate class has a "set" method for the property provided,
                    // then it is called instead or setting the property directly.
                    if (isset($method_reference["set" . $property])) {
                        $this->$method_reference["set" . $property]($value);
                    } elseif (isset($property_reference[$property])) {
                        $this->$property_reference[$property] = $value;
                    } else {
                        // Entries that don't match an available class property are stored in the attributes property if applicable
                        // Typically, these entries will be element attributes such as class, value, onkeyup, etc
                        $this->setAttribute($property, $value);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * This method can be used to view a class' state.
     */
    public function debug()
    {
        echo '<pre>', print_r($this, true), '</pre>';
    }

    /**
     * This method prevents double/single quotes in html attributes from breaking the markup.
     * @param string $str
     * @return string
     */
    protected function filter($str)
    {
        return htmlspecialchars($str);
    }

    /**
     * Returns an attribute
     * @param string $attribute
     * @return string
     */
    public function getAttribute($attribute)
    {
        return isset($this->attributes[$attribute]) ? $this->attributes[$attribute] : '';
    }

    /**
     * This method is used by the Form class and all Element classes to return a string of html
     * attributes.  There is an ignore parameter that allows special attributes from being included.
     * @param string $ignore
     * @return string
     */
    public function getAttributes($ignore = null)
    {
        empty($this->attributes) && return '';
        !is_array($ignore) && $ignore = array($ignore);

        $str = '';
        $attributes = array_diff(array_keys($this->attributes), $ignore);
        foreach ($attributes as $attribute) {
            $str .= ' ' . $attribute;
            if ($this->attributes[$attribute] !== '') {
                $str .= '="' . $this->filter($this->attributes[$attribute]) . '"';
            }
        }

        return $str;
    }

    /**
     * Append a value to an attribute
     * @param string $attribute
     * @param mixed $value
     * @return $this
     */
    public function appendAttribute($attribute, $value)
    {
        if (!empty($this->attributes[$attribute])) {
            $this->attributes[$attribute] .= ' ' . $value;
        } else {
            $this->attributes[$attribute] = $value;
        }

        return $this;
    }

    /**
     * Sets an attribute value
     * @param string $attribute
     * @param mixed $value
     * @return $this
     */
    public function setAttribute($attribute, $value)
    {
        $this->attributes[$attribute] = $value;
        return $this;
    }
}
