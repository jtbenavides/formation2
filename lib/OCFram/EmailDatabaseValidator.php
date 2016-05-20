<?php
namespace OCFram;

class EmailDatabaseValidator extends Validator
{
    public function isValid($value)
    {
        $managers = new Managers('PDO', PDOFactory::getMysqlConnexion());
        return ($managers->getManagerOf('Member')->getMembercUsingEmail($value) == null);
    }
}