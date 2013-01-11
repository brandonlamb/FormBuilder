<?php
namespace Pfbc\Validation;
use Pfbc\AbstractValidation;

class Date extends AbstractValidation
{
    protected $message = "Error: %element% must contain a valid date.";

    public function isValid($value)
    {
        try {
            $date = new \DateTime($value);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
