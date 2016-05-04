<?php
namespace Model;

use \Entity\Member;

class MemberManagerPDO extends MemberManager
{
    public function getMembercUsingLogin($memberc_login)
    {
        $requete = $this->dao->prepare('SELECT MMC_login AS id, MMC_nickname AS nickname, MMC_hash AS hash FROM t_mem_memberc WHERE MMC_login = :MMC_login');
        $requete->bindValue(':MMC_login', (string) $memberc_login, \PDO::PARAM_STR);
        $requete->execute();

        $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Member');

        if ($member = $requete->fetch()) {
            return $member;
        }
        
        return null;
    }

    public function insertMemberc(Member $member)
    {

        $q = $this->dao->prepare('INSERT INTO T_MEM_memberc SET MMC_login = :login, MMC_nickname = :nickname, MMC_hash = :hash');

        $q->bindValue(':login', $member->login(), \PDO::PARAM_INT);
        $q->bindValue(':nickname', $member->nickname());
        $q->bindValue(':hash', $member->hash());

        $q->execute();

    }
}