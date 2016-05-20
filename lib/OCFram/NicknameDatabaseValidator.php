<?php
namespace OCFram;

class NicknameDatabaseValidator extends Validator
{
    public function isValid($value)
    {
        $managers = new Managers('PDO', PDOFactory::getMysqlConnexion());
        return ($managers->getManagerOf('Member')->getMembercUsingNickname($value) == null);
    }
}