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
            'tags' => $this->getTagcUsingNews($tableau['id'])
        ]);

        return $News;
    }

    public function getTagcUsingNews($newsid)
    {
        $requete = $this->dao->prepare('SELECT NTC_description FROM t_new_tagc INNER JOIN t_new_tagd ON NTC_id = NTD_fk_NTC AND NTD_fk_NNC = :NNC_id');

        $requete->bindValue(':NNC_id', $newsid, \PDO::PARAM_INT);
        $requete->execute();

        $tableau = $requete->fetchAll();
        $listTag = [];
        if(!empty($tableau)):
            foreach($tableau as $string):
                $listTag[] = "#".$string['NTC_description'];
            endforeach;
        endif;

        return $listTag;
    }

    public function delete($id)
    {
        $this->dao->exec('DELETE FROM news WHERE id = '.(int) $id);
    }

    public function insertTagc($description)
    {
        $requete = $this->dao->prepare('INSERT INTO t_new_tagc SET NTC_description = :description');

        $requete->bindValue(':description', $description, \PDO::PARAM_STR);
        $requete->execute();

    }

    public function insertTagd($newsid, $description)
    {
        $requete = $this->dao->prepare('INSERT INTO t_new_tagd (NTD_fk_NTC,NTD_fk_NNC) SELECT NTC_id, :newsid FROM t_new_tagc WHERE NTC_description = :description');
        $requete->bindValue(':newsid', $newsid, \PDO::PARAM_INT);
        $requete->bindValue(':description', $description, \PDO::PARAM_STR);
        $requete->execute();
    }

    public function existTagcByDescription($description)
    {
        $requete = $this->dao->prepare('SELECT NTC_id FROM t_new_tagc WHERE NTC_description = :description');

        $requete->bindValue(':description',$description,\PDO::PARAM_STR);
        $requete->execute();

        if($tableau = $requete->fetch()):

            return true;

        endif;

        return false;
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

    public function getListByTag($tag)
    {
        $requete = $this->dao->prepare('SELECT id, MMC_id, MMC_nickname, titre, contenu, dateAjout, dateModif FROM news INNER JOIN T_MEM_memberc ON MMC_id = auteur INNER JOIN t_new_tagd ON id = NTD_fk_NNC INNER JOIN t_new_tagc ON NTD_fk_NTC = NTC_id WHERE NTC_description = :tag ORDER BY id DESC');

        $requete->bindValue(':tag', $tag);
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

    public function getTagcUsingStartDescription($startdescription, $limit)
    {
        $query = 'SELECT NTC_id,NTC_description FROM t_new_tagc INNER JOIN t_new_tagd ON NTC_id = NTD_fk_NTC WHERE NTC_description LIKE :startdescription GROUP BY NTC_id,NTC_description ORDER BY COUNT(*) DESC';
        if((int) $limit>0):
            $query .= ' LIMIT :limit';
        endif;
        $requete = $this->dao->prepare($query);
        $requete->bindValue(':startdescription', $startdescription."%", \PDO::PARAM_STR);
        if((int) $limit>0):
            $requete->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);
        endif;
        $requete->execute();

        $listeTableau = $requete->fetchAll();
        $listeDescription = [];

        foreach($listeTableau as $tableau):
            $listeDescription[] = $tableau['NTC_description'];
        endforeach;

        return $listeDescription;
    }

    public function deleteTagdByNews($newsid){
        $this->dao->exec('DELETE FROM t_new_tagd WHERE NTD_fk_NNC = '.(int) $newsid);
    }

    public function getListCreatBy($auteurid)
    {
        $requete = $this->dao->prepare('SELECT id, MMC_id, MMC_nickname, titre, contenu, dateAjout, dateModif FROM news INNER JOIN T_MEM_memberc ON MMC_id = auteur WHERE auteur = :auteur ORDER BY dateAjout ');

        $requete->bindValue(':auteur', $auteurid);
        $requete->execute();

        $listeTableau = $requete->fetchAll();

        $listeNews = [];

        foreach ($listeTableau as $tableau)
        {
            $News = $this->parseNews($tableau);
            $News->setDateModif();

            $listeNews[] = $News;
        }

        $requete->closeCursor();

        return $listeNews;
    }

    public function getListModifBy($auteurid)
    {
        $requete = $this->dao->prepare('SELECT id, MMC_id, MMC_nickname, titre, contenu, dateAjout, dateModif FROM news INNER JOIN T_MEM_memberc ON MMC_id = auteur WHERE auteur = :auteur AND dateAjout != dateModif ORDER BY dateModif ');

        $requete->bindValue(':auteur', $auteurid);
        $requete->execute();

        $listeTableau = $requete->fetchAll();

        $listeNews = [];

        foreach ($listeTableau as $tableau)
        {
            $News = $this->parseNews($tableau);
            $News->setDateAjout();

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

        $news->setId($this->dao->lastInsertId());

        $listTag = array_unique($news->tags());

        if(!empty($listTag)):
            foreach($listTag as $tag):
                if(!$this->existTagcByDescription($tag)):
                    $this->insertTagc($tag);
                endif;

                $this->insertTagd($news->id(),$tag);
            endforeach;
        endif;
    }

    protected function modify(News $news)
    {
        $requete = $this->dao->prepare('UPDATE news SET titre = :titre, contenu = :contenu, dateModif = NOW() WHERE id = :id');

        $requete->bindValue(':titre', $news->titre());
        $requete->bindValue(':contenu', $news->contenu());
        $requete->bindValue(':id', $news->id(), \PDO::PARAM_INT);

        $requete->execute();

        $this->deleteTagdByNews($news->id());

        $listTag = array_unique($news->tags());

        if(!empty($listTag)):
            foreach($listTag as $tag):
                if(!$this->existTagcByDescription($tag)):
                    $this->insertTagc($tag);
                endif;

                $this->insertTagd($news->id(),$tag);
            endforeach;
        endif;
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
              $News = $this->parseNews($tableau);
              $News->setTags(implode(" ",$News->tags()));
              return $News;

          endif;

        return null;
      }

}