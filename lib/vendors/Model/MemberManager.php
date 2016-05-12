<?php
namespace Model;

use \OCFram\Manager;
use \Entity\Member;

abstract class MemberManager extends Manager{
    abstract public function getMembercUsingLogin($memberc_login);

    abstract public function add(Member $Member);

    public function save(Member $Member)
    {
        if ($Member->isValid())
        {
            $this->add($Member);
        }
        else
        {
            throw new \RuntimeException('La news doit être validée pour être enregistrée');
        }
    }
}