<?php
namespace Model;

use \OCFram\Manager;
use \Entity\Member;

abstract class MemberManager extends Manager{
    abstract public function getMembercUsingLogin($memberc_login);

    abstract public function getMembercUsingNickname($memberc_nickname);

    abstract public function getMembercUsingId($memberc_id);

    abstract public function getMembercUsingEmail($memberc_email);
    
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