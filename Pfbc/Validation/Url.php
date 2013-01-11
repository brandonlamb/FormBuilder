<?php
namespace Pfbc\Validation;
use Pfbc\AbstractValidation;

class Url extends AbstractValidation
{
    protected $message = "Error: %element% must contain a url (e.g. http://www.google.com).";

    public function isValid($value)
    {
        return ($this->isNotApplicable($value) || filter_var($value, FILTER_VALIDATE_URL)) ? true : false;
    }
}
