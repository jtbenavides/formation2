<?php
namespace OCFram;

class EmailPatternValidator extends Validator
{
    public function isValid($value)
    {
        $valid = filter_var($value,FILTER_VALIDATE_EMAIL);
        if($valid == false){
            return false;
        }
        return true;
    }
}