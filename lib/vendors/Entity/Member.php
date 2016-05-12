<?php
namespace Entity;

use \OCFram\Entity;

class Member extends Entity{
    protected $id,
              $login,
              $nickname,
              $email,
              $status,
              $password1,
              $password2,
              $hash;

    const LOGIN_INVALIDE = 4;
    const NICKNAME_INVALIDE = 25;
    const PASSWORD_INVALIDE = 75;
    const EMAIL_INVALIDE = 100;

    public function isValid()
    {
        return !(empty($this->login) || empty($this->nickname) || empty($this->email) || (empty($this->hash) && (empty($this->password1)|| empty($this->password2))));
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

    public function setEmail($email){
        if (!is_string($email) || empty($email))
        {
            $this->erreurs[] = self::EMAIL_INVALIDE;
        }

        $this->email = $email;
    }

    public function setStatus($status){
        if (empty($status))
        {
            $this->erreurs[] = self::STATUS_INVALIDE;
        }

        $this->status = (int) $status;
    }

    public function setPassword1($password1){
        if (!is_string($password1) || empty($password1))
        {
            $this->erreurs[] = self::PASSWORD_INVALIDE;
        }

        $this->password1 = $password1;
    }

    public function setPassword2($password2){
        if (!is_string($password2) || empty($password2))
        {
            $this->erreurs[] = self::PASSWORD_INVALIDE;
        }

        $this->password2 = $password2;
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

    public function email(){
        return $this->email;
    }

    public function status(){
        return $this->status;
    }

    public function password1(){
        return $this->password1;
    }

    public function password2(){
        return $this->password2;
    }

    public function hash(){
        return $this->hash;
    }
}