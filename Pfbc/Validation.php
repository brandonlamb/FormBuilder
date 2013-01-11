<?php
namespace PFBC;

abstract class Validation extends Base
{
    protected $message = "%element% is invalid.";

    public function __construct($message = "")
    {
        if(!empty($message))
            $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function isNotApplicable($value)
    {
        if(is_null($value) || is_array($value) || $value === "")

            return true;
        return false;
    }

    abstract public function isValid($value);
}
