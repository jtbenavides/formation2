<?php
namespace Model;
 
use \Entity\News;
 
class NewsManagerPDO extends NewsManager
{

    public function delete($id)
    {
        $this->dao->exec('DELETE FROM news WHERE id = '.(int) $id);
    }

    public function getListByAuteurId($auteurId)
    {
        $requete = $this->dao->prepare('SELECT id, auteur, auteurId, titre, contenu, dateAjout, dateModif FROM news WHERE auteurId = :auteurId ORDER BY id DESC');

        $requete->bindValue(':auteurId', $auteurId);
        $requete->execute();
        $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News');

        $listeNews = $requete->fetchAll();

        foreach ($listeNews as $news)
        {
            $news->setDateAjout(new \DateTime($news->dateAjout(),new \DateTimeZone('Europe/Paris')));
            $news->setDateModif(new \DateTime($news->dateModif(),new \DateTimeZone('Europe/Paris')));
            $news->setContenu(htmlspecialchars($news->contenu()));
            $news->setAuteur(htmlspecialchars($news->auteur()));
            $news->setTitre(htmlspecialchars($news->titre()));

        }

        $requete->closeCursor();

        return $listeNews;
    }

    protected function add(News $news)
    {
        $requete = $this->dao->prepare('INSERT INTO news SET auteur = :auteur, auteurId = :auteurId, titre = :titre, contenu = :contenu, dateAjout = NOW(), dateModif = NOW()');

        $requete->bindValue(':titre', $news->titre());
        $requete->bindValue(':auteurId', $news->auteurId());
        $requete->bindValue(':contenu', $news->contenu());
        $requete->bindValue(':auteur', $news->auteur());

        $requete->execute();
        
    }

    protected function modify(News $news)
    {
        $requete = $this->dao->prepare('UPDATE news SET titre = :titre, contenu = :contenu, dateModif = NOW() WHERE id = :id');

        $requete->bindValue(':titre', $news->titre());
        $requete->bindValue(':contenu', $news->contenu());
        $requete->bindValue(':id', $news->id(), \PDO::PARAM_INT);

        $requete->execute();
    }

    public function count()
      {
        return $this->dao->query('SELECT COUNT(*) FROM news')->fetchColumn();
      }

      public function getList($debut = -1, $limite = -1)
      {
        $sql = 'SELECT id, auteur, auteurId, titre, contenu, dateAjout, dateModif FROM news ORDER BY id DESC';

        if ($debut != -1 || $limite != -1)
        {
          $sql .= ' LIMIT '.(int) $limite.' OFFSET '.(int) $debut;
        }

        $requete = $this->dao->query($sql);
        $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News');

        $listeNews = $requete->fetchAll();

        foreach ($listeNews as $news)
        {
            $news->setDateAjout(new \DateTime($news->dateAjout(),new \DateTimeZone('Europe/Paris')));
            $news->setDateModif(new \DateTime($news->dateModif(),new \DateTimeZone('Europe/Paris')));
            $news->setContenu(htmlspecialchars($news->contenu()));
            $news->setAuteur(htmlspecialchars($news->auteur()));
            $news->setTitre(htmlspecialchars($news->titre()));

        }

        $requete->closeCursor();

        return $listeNews;
      }

      public function getUnique($id)
      {
        $requete = $this->dao->prepare('SELECT id, MMC_nickname AS auteur, auteurId, titre, contenu, dateAjout, dateModif FROM news INNER JOIN t_mem_memberc ON auteurId = MMC_id WHERE id = :id');
        $requete->bindValue(':id', (int) $id, \PDO::PARAM_INT);
        $requete->execute();

        $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News');

        if ($news = $requete->fetch())
        {
            $news->setDateAjout(new \DateTime($news->dateAjout(),new \DateTimeZone('Europe/Paris')));
            $news->setDateModif(new \DateTime($news->dateModif(),new \DateTimeZone('Europe/Paris')));
            $news->setContenu(htmlspecialchars($news->contenu()));
            $news->setAuteur(htmlspecialchars($news->auteur()));
            $news->setTitre(htmlspecialchars($news->titre()));

          return $news;
        }

        return null;
      }
}