<?php
namespace Entity;
 
use \OCFram\Entity;
 
class Comment extends Entity
{
  protected $news,
            $auteur,
            $pseudo,
            $contenu,
            $date;
 
  const AUTEUR_INVALIDE = 1;
  const CONTENU_INVALIDE = 2;
  const PSEUDO_INVALIDE = 3;
 
  public function isValid()
  {
    return !(!(empty($this->auteur) || empty($this->pseudo)) || empty($this->contenu));
  }
 
  public function setNews($news)
  {
    $this->news = (int) $news;
  }

  public function setAuteur($auteur)
  {
    if (!($auteur instanceof Member) || empty($auteur))
    {
      $this->erreurs[] = self::AUTEUR_INVALIDE;
    }

    $this->auteur = $auteur;
  }

  public function setPseudo($pseudo)
  {
    if (!is_string($pseudo) || empty($pseudo))
    {
      $this->erreurs[] = self::PSEUDO_INVALIDE;
    }

    $this->pseudo = $pseudo;
  }
 
  public function setContenu($contenu)
  {
    if (!is_string($contenu) || empty($contenu))
    {
      $this->erreurs[] = self::CONTENU_INVALIDE;
    }
 
    $this->contenu = $contenu;
  }
 
  public function setDate(\DateTime $date)
  {
    $this->date = $date;
  }
 
  public function news()
  {
    return $this->news;
  }
 
  public function auteur()
  {
    return $this->auteur;
  }

  public function pseudo()
  {
    return $this->pseudo;
  }
 
  public function contenu()
  {
    return $this->contenu;
  }
 
  public function date()
  {
    return $this->date;
  }
}