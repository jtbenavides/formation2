<?php
use \OCFram\Direction;
use \Entity\News;
use \Entity\Comment;
?>
<h1>Feed de <?=$nickname?></h1>
<?php
foreach ($listeFeed as $feed)
{

    if($feed instanceof Comment):  ?>

        <a href=<?= Direction::askRoute('Frontend','News','before',['news' => $feed->news(), 'id' => $feed->id()])?>>...</a> <fieldset>
                <legend>
                    Posté
                    </strong> le <?= $feed['date']->format('d/m/Y à H\hi') ?>
                    <?php if ($user->getAttribute('user_status') == 1 || ($feed['pseudo'] == null && $user->getAttribute('user_id') == $feed['auteur']->id())): ?> -
                        <a href=<?= Direction::askRoute('Backend','News','updateComment',array('id' => $feed['id'])) ?>>Modifier</a> |
                        <a href=<?= Direction::askRoute('Backend','News','deleteComment',array('id' =>$feed['id'])) ?>>Supprimer</a>
                    <?php endif; ?>
                </legend>
        <p><?= nl2br($feed['contenu']) ?></p>
        </fieldset> <a href="<?= Direction::askRoute('Frontend','News','after',['news' => $feed->news(), 'id' => $feed->id()])?>">...</a>
        <br>

    <?php elseif($feed->dateModif() == null): ?>
        <legend>
                Posté
                </strong> le <?= $feed['dateAjout']->format('d/m/Y à H\hi') ?>
            </legend><br><h2><?= $feed->titre() ?></h2>
        <br>

    <?php else: ?>
        <legend>
            Modifié
            </strong> le <?= $feed['dateModif']->format('d/m/Y à H\hi') ?>
        </legend><br><h2><?= $feed->titre() ?></h2>
        <br>

    <?php endif;
}