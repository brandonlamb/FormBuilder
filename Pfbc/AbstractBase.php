<?php
namespace Pfbc;

abstract class AbstractBase
{
    /** @var array */
    protected $attributes = array();

    /** @var Form */
    protected $form;

    /**
     * Set form configuration options
     * @param array $properties
     * @return $this
     */
    public function configure(array $properties = null)
    {
        if (!empty($properties)) {
            $class = get_class($this);

            // The propertyReference lookup array is created so that properties can be set case-insensitively.
            $available = array_keys(get_class_vars($class));
            $propertyReference = array();
            foreach ($available as $property) {
                $propertyReference[strtolower($property)] = $property;
            }

            // The method reference lookup array is created so that 'set' methods can be called case-insensitively
            $available = get_class_methods($class);
            $methodReference = array();
            foreach ($available as $method) {
                $methodReference[strtolower($method)] = $method;
            }

            foreach ($properties as $property => $value) {
                $property = strtolower($property);
                // Properties beginning with '_' cannot be set directly.
                if ($property[0] != '_') {
                    // If the appropriate class has a 'set' method for the property provided,
                    // then it is called instead or setting the property directly.
                    if (isset($methodReference['set' . $property])) {
                        $this->$methodReference['set' . $property]($value);
                    } elseif (isset($propertyReference[$property])) {
                        $this->$propertyReference[$property] = $value;
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
     * @param string $key
     * @return string
     */
    public function getAttribute($key)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : '';
    }

    /**
     * This method is used by the Form class and all Element classes to return a string of html
     * attributes.  There is an ignore parameter that allows special attributes from being included.
     * @param string $ignore
     * @return string
     */
    public function getAttributes($ignore = null)
    {
        if (empty($this->attributes)) {
            return '';
        }

        !is_array($ignore) && $ignore = array($ignore);

        $str = '';
        $attributes = array_diff(array_keys($this->attributes), $ignore);
        foreach ($attributes as $key) {
            $str .= ' ' . $key;
            if ($this->attributes[$key] !== '') {
                $str .= '="' . $this->filter($this->attributes[$key]) . '"';
            }
        }

        return $str;
    }

    /**
     * Set multiple attributes via array
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
        return $this;
    }

    /**
     * Append a value to an attribute
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function appendAttribute($key, $value)
    {
        if (!empty($this->attributes[$key])) {
            $this->attributes[$key] .= ' ' . $value;
        } else {
            $this->attributes[$key] = $value;
        }

        return $this;
    }

    /**
     * Sets an attribute value
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * Set the form object
     * @param Form $form
     * @return $this
     */
    public function setForm(Form $form)
    {
        $this->form = $form;
        return $this;
    }
}
