<?php
namespace OCFram;

class LoginDatabaseValidator extends Validator
{
    public function isValid($value)
    {
        $managers = new Managers('PDO', PDOFactory::getMysqlConnexion());
        return ($managers->getManagerOf('Member')->getMembercUsingLogin($value) == null);
        
    }
}