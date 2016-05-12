<?php
namespace Model;
 
use Entity\Member;
use \Entity\News;
 
class NewsManagerPDO extends NewsManager
{
    public function parseNews($tableau){
        $Member = new Member([
            'id' => $tableau['MMC_id'],
            'nickname' => htmlspecialchars($tableau['MMC_nickname'])
        ]);

        $News = new News([
            'id' => $tableau['id'],
            'titre' => htmlspecialchars($tableau['titre']),
            'contenu' => htmlspecialchars($tableau['contenu']),
            'dateAjout' => new \DateTime($tableau['dateAjout'],new \DateTimeZone('Europe/Paris')),
            'dateModif' => new \DateTime($tableau['dateModif'],new \DateTimeZone('Europe/Paris')),
            'auteur' => $Member,
        ]);

        return $News;
    }

    public function delete($id)
    {
        $this->dao->exec('DELETE FROM news WHERE id = '.(int) $id);
    }

    public function getListByAuteurId($auteur)
    {
        $requete = $this->dao->prepare('SELECT id, MMC_id, MMC_nickname, titre, contenu, dateAjout, dateModif FROM news INNER JOIN T_MEM_memberc ON MMC_id = auteur WHERE auteur = :auteur ORDER BY id DESC');

        $requete->bindValue(':auteur', $auteur);
        $requete->execute();

        $listeTableau = $requete->fetchAll();

        $listeNews = [];

        foreach ($listeTableau as $tableau)
        {
            $News = $this->parseNews($tableau);

            $listeNews[] = $News;
        }

        $requete->closeCursor();

        return $listeNews;
    }

    protected function add(News $news)
    {
        $requete = $this->dao->prepare('INSERT INTO news SET auteur = :auteur, titre = :titre, contenu = :contenu, dateAjout = NOW(), dateModif = NOW()');

        $requete->bindValue(':titre', $news->titre());
        $requete->bindValue(':contenu', $news->contenu());
        $requete->bindValue(':auteur', $news->auteur()->id());

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
        $sql = 'SELECT id, MMC_id, MMC_nickname, titre, contenu, dateAjout, dateModif FROM news INNER JOIN T_MEM_memberc ON MMC_id = auteur ORDER BY id DESC';

        if ($debut != -1 || $limite != -1)
        {
          $sql .= ' LIMIT '.(int) $limite.' OFFSET '.(int) $debut;
        }

        $requete = $this->dao->query($sql);

        $listeTableau = $requete->fetchAll();

        $listeNews = [];

        foreach ($listeTableau as $tableau)
        {
            $News = $this->parseNews($tableau);

            $listeNews[] = $News;
        }

        $requete->closeCursor();

        return $listeNews;
      }

      public function getUnique($id)
      {
          $requete = $this->dao->prepare('SELECT id, MMC_id, MMC_nickname, titre, contenu, dateAjout, dateModif FROM news INNER JOIN t_mem_memberc ON auteur = MMC_id WHERE id = :id');
          $requete->bindValue(':id', (int) $id, \PDO::PARAM_INT);
          $requete->execute();

          if($tableau = $requete->fetch()):

            return $this->parseNews($tableau);

          endif;

        return null;
      }
}