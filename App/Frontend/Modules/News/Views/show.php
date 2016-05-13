<?php use \OCFram\Direction; ?>

<p>Par <em><a href=<?= Direction::askRoute('Frontend','News','user',['id' => $news['auteur']->id()])?> ><?= $news['auteur']->nickname() ?></a></em>, le <?= $news['dateAjout']->format('d/m/Y à H\hi') ?></p>
<h2><?= $news['titre'] ?></h2>
<p><?= nl2br($news['contenu']) ?></p>

<?php
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
      Posté par <strong><?php if($comment['auteur'] != null): ?>
            <a href=<?= Direction::askRoute('Frontend','News','user',['id' => $comment['auteur']->id()])?>>
                                    <?= $comment['auteur']->nickname() ?>
            </a>
                                <?php else: ?>
                                    <?= $comment['pseudo'] ?>
                                <?php endif;?>
        </strong> le <?= $comment['date']->format('d/m/Y à H\hi') ?>
      <?php if ($user->getAttribute('user_status') == 1 || ($comment['pseudo'] == null && $user->getAttribute('user_id') == $comment['auteur']->id())): ?> -
        <a href=<?= Direction::askRoute('Backend','News','updateComment',array('id' => $comment['id'])) ?>>Modifier</a> |
        <a href=<?= Direction::askRoute('Backend','News','deleteComment',array('id' =>$comment['id'])) ?>>Supprimer</a>
      <?php endif; ?>
    </legend>
    <p><?= nl2br($comment['contenu']) ?></p>
  </fieldset>
  <?php
}
?>
</div>
<form id="form" >
    <?php if (!$user->isAuthenticated()): ?>
        <label id="lpseudo">Pseudo : </label>
        <input type="text" id="pseudo"><br />
        <input type="hidden" id="pseudoid" value=0 >
    <?php else: ?>
        <input type="hidden" id="pseudo" value=<?= $user->getAttribute('user_name') ?>>
        <input type="hidden" id="pseudoid" value=<?= $user->getAttribute('user_id') ?>>
    <?php endif; ?>
    <label id="lcontenu">Commentaire : </label>
    <textarea id="contenu"></textarea>
    <input type="hidden" id="news" value=<?= Direction::askRoute('Frontend','News','insertComment',array('news' =>$news['id'])) ?> />
    <input type="submit" value="Envoyer" id="button_submit"/>
</form>
<center>
<div id="display"></div>
</center>
