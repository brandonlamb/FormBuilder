<?php
namespace Pfbc\Validation;
use Pfbc\AbstractValidation;

class RegExp extends AbstractValidation
{
    protected $message = 'Error: %element% contains invalid characters.';
    protected $pattern;

    public function __construct($pattern, $message = '')
    {
        $this->pattern = $pattern;
        parent::__construct($message);
    }

    public function isValid($value)
    {
        return ($this->isNotApplicable($value) || preg_match($this->pattern, $value)) ? true : false;
    }
}
