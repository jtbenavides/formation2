<p>Par <em><?= $news['auteur'] ?></em>, le <?= $news['dateAjout']->format('d/m/Y à H\hi') ?></p>
<h2><?= $news['titre'] ?></h2>
<p><?= nl2br($news['contenu']) ?></p>

<?php use \OCFram\Direction;
if ($news['dateAjout'] != $news['dateModif']) { ?>
  <p style="text-align: right;"><small><em>Modifiée le <?= $news['dateModif']->format('d/m/Y à H\hi') ?></em></small></p>
<?php } ?>

<p><a href="commenter-<?= $news['id'] ?>.html">Ajouter un commentaire</a></p>

<?php
if (empty($comments))
{
  ?>
  <p>Aucun commentaire n'a encore été posté. Soyez le premier à en laisser un !</p>
  <?php
}

foreach ($comments as $comment)
{
  ?>
  <fieldset>
    <legend>
      Posté par <strong><?= htmlspecialchars($comment['auteur']) ?></strong> le <?= $comment['date']->format('d/m/Y à H\hi') ?>
      <?php if ($user->isAuthenticated()) { ?> -
        <a href=<?= Direction::askRoute('Backend','News','updateComment',array('id' => $comment['id'])) ?>>Modifier</a> |
        <a href=<?= Direction::askRoute('Backend','News','deleteComment',array('id' =>$comment['id'])) ?>>Supprimer</a>
      <?php } ?>
    </legend>
    <p><?= nl2br($comment['contenu']) ?></p>
  </fieldset>
  <?php
}
?>

<p><a href="commenter-<?= $news['id'] ?>.html">Ajouter un commentaire</a></p>