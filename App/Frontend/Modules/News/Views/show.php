<p>Par <em><?= $news['auteur'] ?></em>, le <?= $news['dateAjout']->format('d/m/Y à H\hi') ?></p>
<h2><?= $news['titre'] ?></h2>
<p><?= nl2br($news['contenu']) ?></p>

<?php use \OCFram\Direction;
if ($news['dateAjout'] != $news['dateModif']) { ?>
  <p style="text-align: right;"><small><em>Modifiée le <?= $news['dateModif']->format('d/m/Y à H\hi') ?></em></small></p>
<?php } ?>

<div id="content">
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
</div>
<form id="form" >
    <label id="lpseudo">Pseudo : </label>
    <input type="text" id="pseudo"><br />
    <label id="lcontenu">Commentaire : </label>
    <textarea id="contenu"></textarea>
    <input type="hidden" id="news" value=<?= Direction::askRoute('Frontend','News','insertComment',array('news' =>$news['id'])) ?> />
    <input type="submit" value="Envoyer" id="button_submit"/>
</form>
<center>
<div id="display"></div>
</center>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script src="/js/mon-script.js"></script>