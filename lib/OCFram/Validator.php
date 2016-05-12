<?php
namespace OCFram;

abstract class Validator
{
    protected $errorMessage;
    protected $field;

    public function __construct($errorMessage)
    {
        $this->setErrorMessage($errorMessage);
    }

    abstract public function isValid($value);

    public function setErrorMessage($errorMessage)
    {
        if (is_string($errorMessage))
        {
            $this->errorMessage = $errorMessage;
        }
    }

    public function setField($field){
        if($field instanceof Field){
            $this->field = $field;
        }
    }

    public function errorMessage()
    {
        return $this->errorMessage;
    }

    public function field(){
        return $this->field;
    }
}