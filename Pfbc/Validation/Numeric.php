<?php
namespace Pfbc\Validation;
use Pfbc\AbstractValidation;

class Numeric extends AbstractValidation
{
    protected $message = 'Error: %element% must be numeric.';

    public function isValid($value)
    {
        return ($this->isNotApplicable($value) || is_numeric($value)) ? true : false;
    }
}
