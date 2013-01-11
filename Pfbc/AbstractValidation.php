<?php
namespace Pfbc;

abstract class AbstractValidation extends AbstractBase
{
    protected $message = '%element% is invalid.';

    abstract public function isValid($value);

    /**
     * Constructor
     * @param string $message
     */
    public function __construct($message = '')
    {
        !empty($message) && $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get if not applicable
     * @return bool
     */
    public function isNotApplicable($value)
    {
        return is_null($value) || is_array($value) || $value === '' ? true : false;
    }
}
