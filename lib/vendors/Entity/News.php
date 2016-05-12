<?php
namespace Entity;
 
use \OCFram\Entity;
 
class News extends Entity
{
  protected $auteur,
            $titre,
            $contenu,
            $tags,
            $dateAjout,
            $dateModif;
 
  const AUTEUR_INVALIDE = 1;
  const TITRE_INVALIDE = 2;
  const CONTENU_INVALIDE = 3;
  const TAGS_INVALIDE = 5;
 
  public function isValid()
  {
    return !(empty($this->titre) || empty($this->contenu));
  }
 /*
<?= isset($erreurs) && in_array(\Entity\News::AUTEUR_INVALIDE, $erreurs) ? 'L\'auteur est invalide.<br />' : '' ?>
        <label>Auteur</label>
        <input type="text" name="auteur" value="<?= isset($news) ? $news['auteur'] : '' ?>" /><br />  */
 
  // SETTERS //
 
  public function setAuteur($auteur)
  {
    if (!($auteur instanceof Member) || empty($auteur))
    {
      $this->erreurs[] = self::AUTEUR_INVALIDE;
    }
 
    $this->auteur = $auteur;
  }
 
  public function setTitre($titre)
  {
    if (!is_string($titre) || empty($titre))
    {
      $this->erreurs[] = self::TITRE_INVALIDE;
    }
 
    $this->titre = $titre;
  }
 
  public function setContenu($contenu)
  {
    if (!is_string($contenu) || empty($contenu))
    {
      $this->erreurs[] = self::CONTENU_INVALIDE;
    }
 
    $this->contenu = $contenu;
  }

  public function setTags($tags)
  {
    if (!is_string($tags) || empty($tags))
    {
      $this->erreurs[] = self::TAGS_INVALIDE;
    }

    $this->tags = $tags;
  }

  public function setDateAjout(\DateTime $dateAjout)
  {
    $this->dateAjout = $dateAjout;
  }
 
  public function setDateModif(\DateTime $dateModif)
  {
    $this->dateModif = $dateModif;
  }
 
  // GETTERS //
 
  public function auteur()
  {
    return $this->auteur;
  }

  public function titre()
  {
    return $this->titre;
  }
 
  public function contenu()
  {
    return $this->contenu;
  }

  public function tags()
  {
    return $this->tags;
  }
 
  public function dateAjout()
  {
    return $this->dateAjout;
  }
 
  public function dateModif()
  {
    return $this->dateModif;
  }
}