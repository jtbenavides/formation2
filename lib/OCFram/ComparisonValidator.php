<?php
namespace OCFram;

class ComparisonValidator extends Validator
{
    public function isValid($value)
    {
        if( empty($this->field)){
            return false;
        }

        return (strcmp($value,$this->field()->value()) == 0);
    }
}