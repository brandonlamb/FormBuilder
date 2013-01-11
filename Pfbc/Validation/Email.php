<?php
namespace PFBC\Validation;
use Pfbc\AbstractValidation;

class Email extends AbstractValidation
{
    protected $message = "Error: %element% must contain an email address.";

    public function isValid($value)
    {
        return ($this->isNotApplicable($value) || filter_var($value, FILTER_VALIDATE_EMAIL)) ? true : false;
    }
}
