<?php
namespace Pfbc;

abstract class AbstractElement extends AbstractBase
{
    /**
     * @var array
     */
    protected $errors = array();

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var string, element's label
     */
    protected $label;

    /**
     * @var string, element's short description
     */
    protected $shortDesc;

    /**
     * @var string, element's long description
     */
    protected $longDesc;

    /**
     * @var array
     */
    protected $validation = array();

    /**
     * Constructor
     * @param string $label
     * @param string $name, form name
     * @param array $properties
     */
    public function __construct($label, $name, array $properties = null)
    {
        $configuration = array(
            'label' => $label,
            'name' => $name,
        );

        // Merge any properties provided with an associative array containing the label and name properties
        if (is_array($properties)) {
            $configuration = array_merge($configuration, $properties);
        }

        $this->configure($configuration);
    }

    /*When an element is serialized and stored in the session, this method prevents any non-essential
    information from being included.*/
    public function __sleep()
    {
        return array('attributes', 'label', 'validation');
    }

    /**
     * Get form label
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Get short description
     * @return string
     */
    public function getShortDesc()
    {
        return $this->shortDesc;
    }

    /**
     * Get long description
     * @return string
     */
    public function getLongDesc()
    {
        return $this->longDesc;
    }

    /**
     * This method provides a shortcut for checking if an element is required.
     * @return bool
     */
    public function isRequired()
    {
        if (!empty($this->validation)) {
            foreach ($this->validation as $validation) {
                if ($validation instanceof Validation\Required) {
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * The isValid method ensures that the provided value satisfies each of the
     * element's validation rules.
     * @return bool
     */
    public function isValid($value)
    {
        if (empty($this->validation)) {
            return true;
        }

        if (!empty($this->label)) {
            $element = $this->label;
        } elseif (!empty($this->attributes['placeholder'])) {
            $element = $this->attributes['placeholder'];
        } else {
            $element = $this->attributes['name'];
        }

        if (substr($element, -1) == ':') {
            $element = substr($element, 0, -1);
        }

        foreach ($this->validation as $validation) {
            if (!$validation->isValid($value)) {
                // In the error message, %element% will be replaced by the element's label (or name if label is not provided)
                $this->errors[] = str_replace('%element%', $element, $validation->getMessage());
                return false;
            }
        }

        return true;
    }

    /**
     * Many of the included elements make use of the <input> tag for display.  These include the Hidden, Textbox,
     * Password, Date, Color, Button, Email, and File element classes.  The project's other element classes will
     * override this method with their own implementation.
     */
    public function render()
    {
        if (isset($this->attributes['value']) && is_array($this->attributes['value'])) {
            $this->attributes['value'] = '';
        }

        echo '<input', $this->getAttributes(), '/>';
    }

    /**
     * Set the element's label text
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = (string) $label;
        return $this;
    }

    /**
     * This method provides a shortcut for applying the Required validation class to an element.
     * @param bool $required
     * @return $this
     */
    public function setRequired($required)
    {
        $required === true && $this->validation[] = new Validation\Required();
        $this->attributes['required'] = 'required';
        return $this;
    }

    /**
     * This method applies one or more validation rules to an element.  If can accept a single concrete
     * validation class or an array of entries.
     * @param string|array $validations
     * @return $this
     */
    public function setValidation($validations)
    {
        // If a single validation class is provided, an array is created in order to reuse the same logic
        !is_array($validations) && $validations = array($validations);

        foreach ($validations as $validation) {
            // Ensures $validation contains a existing concrete validation class
            if ($validation instanceof Validation) {
                $this->validation[] = $validation;
                if ($validation instanceof Validation\Required) {
                    $this->attributes['required'] = 'required';
                }
            }
        }

        return $this;
    }
}
