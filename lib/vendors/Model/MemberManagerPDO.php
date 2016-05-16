<?php
namespace Model;

use \Entity\Member;

class MemberManagerPDO extends MemberManager
{
    public function getMembercUsingLogin($memberc_login)
    {
        $requete = $this->dao->prepare('SELECT MMC_id AS id, MMC_login AS login, MMC_nickname AS nickname,MMC_fk_MMY as status, MMC_hash AS hash FROM t_mem_memberc WHERE MMC_login = :MMC_login');
        $requete->bindValue(':MMC_login', (string) $memberc_login, \PDO::PARAM_STR);
        $requete->execute();

        $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Member');

        if ($member = $requete->fetch()) {
            return $member;
        }
        
        return null;
    }

    public function getMembercUsingNickname($memberc_nickname)
    {
        $requete = $this->dao->prepare('SELECT MMC_id AS id, MMC_login AS login, MMC_nickname AS nickname,MMC_fk_MMY as status, MMC_hash AS hash FROM t_mem_memberc WHERE MMC_nickname = :MMC_nickname');
        $requete->bindValue(':MMC_nickname', (string) $memberc_nickname, \PDO::PARAM_STR);
        $requete->execute();

        $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Member');

        if ($member = $requete->fetch()) {
            return $member;
        }

        return null;
    }

    public function getMembercUsingId($memberc_id)
    {
        $requete = $this->dao->prepare('SELECT MMC_id AS id, MMC_login AS login, MMC_nickname AS nickname,MMC_fk_MMY as status, MMC_hash AS hash FROM t_mem_memberc WHERE MMC_id = :MMC_id');
        $requete->bindValue(':MMC_id', $memberc_id, \PDO::PARAM_INT);
        $requete->execute();

        $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Member');

        if ($member = $requete->fetch()) {
            return $member;
        }

        return null;
    }

    public function getMembercUsingEmail($memberc_email)
    {
        $requete = $this->dao->prepare('SELECT MMC_id AS id, MMC_login AS login, MMC_nickname AS nickname,MMC_fk_MMY as status, MMC_hash AS hash FROM t_mem_memberc WHERE MMC_email = :MMC_email');
        $requete->bindValue(':MMC_email', (string) $memberc_email, \PDO::PARAM_STR);
        $requete->execute();

        $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Member');

        if ($member = $requete->fetch()) {
            return $member;
        }

        return null;
    }

    public function add(Member $member)
    {

        $q = $this->dao->prepare('INSERT INTO T_MEM_memberc SET MMC_login = :login, MMC_nickname = :nickname, MMC_hash = :hash, MMC_email = :email, MMC_fk_MMY = :status');

        $q->bindValue(':login', $member->login(), \PDO::PARAM_INT);
        $q->bindValue(':status', $member->status(), \PDO::PARAM_INT);
        $q->bindValue(':nickname', $member->nickname());
        $q->bindValue(':email', $member->email());
        $q->bindValue(':hash', $member->hash());

        $q->execute();

        $member->setId($this->dao->lastInsertId());

    }
}