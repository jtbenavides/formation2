<?php
namespace Entity;

use \OCFram\Entity;

class Member extends Entity{
    protected $id,
              $login,
              $nickname,
              $status,
              $hash;

    const LOGIN_INVALIDE = 4;
    const NICKNAME_INVALIDE = 25;
    const PASSWORD_INVALIDE = 75;

    public function setId($id){
        if (!is_int($id) || empty($id))
        {
            $this->erreurs[] = self::ID_INVALIDE;
        }

        $this->id = $id;
    }

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

    public function setStatus($status){
        if (!is_string($status) || empty($status))
        {
            $this->erreurs[] = self::STATUS_INVALIDE;
        }

        $this->status = $status;
    }

    public function setHash($hash){
        if (!is_string($hash) || empty($hash))
        {
            $this->erreurs[] = self::PASSWORD_INVALIDE;
        }

        $this->hash = $hash;
    }

    public function id(){
        return $this->id;
    }

    public function login(){
        return $this->login;
    }

    public function nickname(){
        return $this->nickname;
    }

    public function status(){
        return $this->status;
    }

    public function hash(){
        return $this->hash;
    }
}