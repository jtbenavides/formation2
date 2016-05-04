<?php
namespace Entity;

use \OCFram\Entity;

class Member extends Entity{
    protected $login,
              $nickname,
              $hash;

    const LOGIN_INVALIDE = 4;
    const NICKNAME_INVALIDE = 25;
    const PASSWORD_INVALIDE = 75;

    public function setLogin($login){
        if (!is_string($login) || empty($login))
        {
            $this->erreurs[] = self::LOGIN_INVALIDE;
        }

        $this->login = $login;
    }

    public function setNickname($nickname){
        if (!is_string($nickname) || empty($nickname))
        {
            $this->erreurs[] = self::NICKNAME_INVALIDE;
        }

        $this->nickname = $nickname;
    }

    public function setHash($hash){
        if (!is_string($hash) || empty($hash))
        {
            $this->erreurs[] = self::PASSWORD_INVALIDE;
        }

        $this->hash = $hash;
    }

    public function login(){
        return $this->login;
    }

    public function nickname(){
        return $this->nickname;
    }

    public function hash(){
        return $this->hash;
    }
}