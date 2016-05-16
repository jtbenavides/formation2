<?php use \OCFram\Direction; ?>
<h1 id="index">Last News<h1>
<?php
foreach ($listeNews as $news)
{
?>
  <h2><a href=<?= Direction::askRoute('Frontend','News','show',array('id' =>$news['id'])) ?>><?= $news['titre'] ?></a></h2>
  <p><?= nl2br($news['contenu']) ?></p>
<?php
}