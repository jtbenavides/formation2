<?php
namespace OCFram;

class EmailValidator extends Validator
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