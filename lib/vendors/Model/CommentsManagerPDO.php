<?php
namespace Model;
 
use \Entity\Comment;
use \Entity\Member;
 
class CommentsManagerPDO extends CommentsManager
{

    public function parseComment($tableau){
        $Comment = new Comment([
            'id' => $tableau['id'],
            'news' => $tableau['news'],
            'contenu' => htmlspecialchars($tableau['contenu']),
            'date' => new \DateTime($tableau['date'],new \DateTimeZone('Europe/Paris')),
        ]);

        if($tableau['pseudo'] == null):
            $Member = new Member([
                'id' => $tableau['MMC_id'],
                'nickname' => htmlspecialchars($tableau['MMC_nickname'])
            ]);

            $Comment->setAuteur($Member);
        else:
            $Comment->setPseudo(htmlspecialchars($tableau['pseudo']));
        endif;

        return $Comment;
    }

  protected function add(Comment $comment)
  {
      $q = $this->dao->prepare('INSERT INTO comments SET news = :news, auteur = :auteur, contenu = :contenu, pseudo = :pseudo, date = NOW()');

      $q->bindValue(':news', $comment->news(), \PDO::PARAM_INT);
      if($comment->pseudo() == null):
          $q->bindValue(':auteur', $comment->auteur()->id(), \PDO::PARAM_INT);
          $q->bindValue(':pseudo', null);
      else:
          $q->bindValue(':auteur', 0, \PDO::PARAM_INT);
          $q->bindValue(':pseudo', $comment->pseudo());
      endif;
      $q->bindValue(':contenu', $comment->contenu());

      $q->execute();

      $comment->setId($this->dao->lastInsertId());
      $comment->setDate($this->getUnique($comment['id'])->date());
  }

  public function getListOf($news)
  {

    if (!ctype_digit($news) && !is_int($news)):
      throw new \InvalidArgumentException('L\'identifiant de la news passé doit être un nombre entier valide');
    endif;

    $q = $this->dao->prepare('SELECT id, news, MMC_id, MMC_nickname, pseudo, contenu, date FROM comments LEFT OUTER JOIN T_MEM_memberc ON MMC_id = auteur WHERE news = :news ORDER BY date ASC');
    $q->bindValue(':news', $news, \PDO::PARAM_INT);
    $q->execute();

    $listTableau = $q->fetchAll();

    $listComment = [];

    foreach ($listTableau as $tableau)
    {
        $Comment = $this->parseComment($tableau);

        $listComment[] = $Comment;
    }

    return $listComment;
  }

  protected function modify(Comment $comment)
  {
    $q = $this->dao->prepare('UPDATE comments SET pseudo = :pseudo, contenu = :contenu WHERE id = :id');

      if($comment->pseudo() == null):
          $q->bindValue(':pseudo', null);
      else:
          $q->bindValue(':pseudo', $comment->pseudo());
      endif;
    $q->bindValue(':contenu', $comment->contenu());
    $q->bindValue(':id', $comment->id(), \PDO::PARAM_INT);

    $q->execute();
  }

  public function getUnique($id)
  {
      $q = $this->dao->prepare('SELECT id, news, MMC_id, MMC_nickname, pseudo, contenu, date FROM comments LEFT OUTER JOIN T_MEM_memberc ON MMC_id = auteur WHERE id = :id');
      $q->bindValue(':id', (int) $id, \PDO::PARAM_INT);
      $q->execute();

      if($tableau = $q->fetch()):
          return $this->parseComment($tableau);
      endif;

      return null;
  }

  public function delete($id)
  {
    $this->dao->exec('DELETE FROM comments WHERE id = '.(int) $id);
  }

  public function deleteFromNews($news)
  {
    $this->dao->exec('DELETE FROM comments WHERE news = '.(int) $news);
  }
}