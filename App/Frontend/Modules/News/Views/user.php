<?php
use \OCFram\Direction;
use \Entity\News;
use \Entity\Comment;

?>
    <h1>Feed de <?= $nickname ?></h1>
    <form class="checkform">
        <p>Filtrer :</p>
        <label>News crée :</label>
        <input type="checkbox" name="news-create" id="check-created" checked> |
        <label>News modifié:</label>
        <input type="checkbox" name="news-modify" id="check-modified" checked> |
        <label>Comments :</label>
        <input type="checkbox" name="comment" id="check-comment" checked>
    </form>
<?php
foreach ($listeFeed as $feed) {

    if ($feed instanceof Comment): ?>
        <section class="comments">
            <a class="link-after"
               href="<?= Direction::askRoute('Frontend', 'News', 'after', ['news' => $feed->news(), 'id' => $feed->id()]) ?>">...</a>
            <fieldset class="comment">
                <legend>
                    Posté
                    </strong> le <?= $feed['date']->format('d/m/Y à H\hi') ?>
                    <?php if ($user->getAttribute('user_status') == 1 || ($feed['pseudo'] == null && $user->getAttribute('user_id') == $feed['auteur']->id())): ?> -
                        <a href=<?= Direction::askRoute('Backend', 'News', 'updateComment', array('id' => $feed['id'])) ?>>Modifier</a> |
                        <a class="link-delete" href=<?= Direction::askRoute('Backend', 'News', 'deleteComment', array('id' => $feed['id'])) ?>>Supprimer</a>
                    <?php endif; ?>
                </legend>
                <p><?= nl2br($feed['contenu']) ?></p>
            </fieldset>
            <a class="link-before"
               href=<?= Direction::askRoute('Frontend', 'News', 'before', ['news' => $feed->news(), 'id' => $feed->id()]) ?>>...</a>
            <br>
        </section>
    <?php elseif ($feed->dateModif() == null): ?>
        <section class="created">
            <legend>
                Posté
                </strong> le <?= $feed['dateAjout']->format('d/m/Y à H\hi') ?>
            </legend>
            <br>
            <h2>
                <a href=<?= Direction::askRoute('Frontend', 'News', 'show', ['id' => $feed->id()]) ?>><?= $feed->titre() ?></a>
            </h2>
            <br>
        </section>

    <?php else: ?>
        <section class="modified">
            <legend>
                Modifié
                </strong> le <?= $feed['dateModif']->format('d/m/Y à H\hi') ?>
            </legend>
            <br>
            <h2>
                <a href=<?= Direction::askRoute('Frontend', 'News', 'show', ['id' => $feed->id()]) ?>><?= $feed->titre() ?></a>
            </h2>
            <br>
        </section>

    <?php endif;
}