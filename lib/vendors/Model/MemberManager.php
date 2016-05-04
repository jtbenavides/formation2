<?php
namespace Model;

use \OCFram\Manager;
use \Entity\Member;

abstract class MemberManager extends Manager{
    abstract public function getMembercUsingLogin($memberc_login);

    abstract public function insertMemberc(Member $member);
}