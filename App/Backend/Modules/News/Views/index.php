<p style="text-align: center">Il y a actuellement <?= $nombreNews ?> news. En voici la liste :</p>

<table>
    <tr><th>Auteur</th><th>Titre</th><th>Date d'ajout</th><th>Dernière modification</th><th>Action</th></tr>
    <?php use \OCFram\Direction;
    foreach ($listeNews as $news)
    {
        echo '<tr><td>', $news['id'], '</td><td>', $news['titre'], '</td><td>le ', $news['dateAjout']->format('d/m/Y à H\hi'), '</td><td>', ($news['dateAjout'] == $news['dateModif'] ? '-' : 'le '.$news['dateModif']->format('d/m/Y à H\hi')), '</td><td><a href=', Direction::askRoute('Backend','News','update',array('id' =>$news['id'])), '><img src="/images/update.png" alt="Modifier" /></a> <a href=', Direction::askRoute('Backend','News','delete',array('id' =>$news['id'])), '><img src="/images/delete.png" alt="Supprimer" /></a></td></tr>', "\n";
    }
    ?>
</table>