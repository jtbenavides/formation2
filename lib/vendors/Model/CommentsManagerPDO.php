<?php
namespace Model;
 
use \Entity\Comment;
 
class CommentsManagerPDO extends CommentsManager
{
  protected function add(Comment $comment)
  {
      $q = $this->dao->prepare('INSERT INTO comments SET news = :news, auteur = :auteur, contenu = :contenu, auteurId = :auteurId, date = NOW()');
 
      $q->bindValue(':news', $comment->news(), \PDO::PARAM_INT);
      $q->bindValue(':auteurId', $comment->auteurId(), \PDO::PARAM_INT);
      $q->bindValue(':auteur', $comment->auteur());
      $q->bindValue(':contenu', $comment->contenu());
 
      $q->execute();
 
      $comment->setId($this->dao->lastInsertId());
      $comment->setDate($this->getUnique($comment['id'])->date());
  }

  public function unique(Comment $comment){
      $q = $this->dao->prepare('SELECT * FROM comments WHERE news = :news AND auteur = :auteur AND contenu = :contenu AND TIMESTAMPDIFF(SECOND ,date,NOW()) < 2 ');

      $q->bindValue(':news', $comment->news(), \PDO::PARAM_INT);
      $q->bindValue(':auteur', $comment->auteur());
      $q->bindValue(':contenu', $comment->contenu());

      $q->execute();

      return ($q->rowCount()== 0);
  }

  public function getListOf($news)
  {
    if (!ctype_digit($news))
    {
      throw new \InvalidArgumentException('L\'identifiant de la news passé doit être un nombre entier valide');
    }
 
    $q = $this->dao->prepare('SELECT id, news, auteur, contenu, auteurId, date FROM comments WHERE news = :news ORDER BY date ASC');
    $q->bindValue(':news', $news, \PDO::PARAM_INT);
    $q->execute();
 
    $q->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Comment');
 
    $comments = $q->fetchAll();
 
    foreach ($comments as $comment)
    {
        $comment->setDate(new \DateTime($comment->date(),new \DateTimeZone('Europe/Paris')));
        $comment->setContenu(htmlspecialchars($comment->contenu()));
        $comment->setAuteur(htmlspecialchars($comment->auteur()));
    }
 
    return $comments;
  }

  protected function modify(Comment $comment)
  {
    $q = $this->dao->prepare('UPDATE comments SET auteur = :auteur, contenu = :contenu WHERE id = :id');

    $q->bindValue(':auteur', $comment->auteur());
    $q->bindValue(':contenu', $comment->contenu());
    $q->bindValue(':id', $comment->id(), \PDO::PARAM_INT);

    $q->execute();
  }

  public function getUnique($id)
  {
      $q = $this->dao->prepare('SELECT id, news, auteur, auteurId, contenu, date FROM comments WHERE id = :id');
      $q->bindValue(':id', (int) $id, \PDO::PARAM_INT);
      $q->execute();

      $q->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Comment');

      $comment = $q->fetch();
      $comment->setDate(new \DateTime($comment->date(),new \DateTimeZone('Europe/Paris')));
      $comment->setContenu(htmlspecialchars($comment->contenu()));
      $comment->setAuteur(htmlspecialchars($comment->auteur()));
      $comment->setAuteurId($comment->auteurId());
      $comment->setNews($comment->news());
    return $comment;
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