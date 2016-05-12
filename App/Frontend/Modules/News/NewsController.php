<?php
namespace App\Frontend\Modules\News;
 
use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \OCFram\Direction;
use \Entity\Comment;
 
class NewsController extends BackController
{
    
  public function executeIndex(HTTPRequest $request)
  {
    $nombreNews = $this->app->config()->get('nombre_news');
    $nombreCaracteres = $this->app->config()->get('nombre_caracteres');

    // On ajoute une définition pour le titre.
    $this->page->addVar('title', 'Liste des ' . $nombreNews . ' dernières news');

    // On récupère le manager des news.
    $manager = $this->managers->getManagerOf('News');

    $listeNews = $manager->getList(0, $nombreNews);

    foreach ($listeNews as $news) {
      if (strlen($news->contenu()) > $nombreCaracteres) {
        $debut = substr($news->contenu(), 0, $nombreCaracteres);
        $debut = substr($debut, 0, strrpos($debut, ' ')) . '...';

        $news->setContenu($debut);
      }
    }

    // On ajoute la variable $listeNews à la vue.
    $this->page->addVar('listeNews', $listeNews);
      return true;
  }

  public function executeShow(HTTPRequest $request)
  {
    $news = $this->managers->getManagerOf('News')->getUnique($request->getData('id'));

    if (empty($news)) {
      $this->app->httpResponse()->redirect404();
    }

    $this->page->addVar('title', $news->titre());
    $this->page->addVar('news', $news);
    $this->page->addVar('comments', $this->managers->getManagerOf('Comments')->getListOf($news->id()));
      return true;
  }

  public function executeInsertComment(HTTPRequest $request)
  {
      $response = [];
      $response['success'] = true;

      if ($request->postExists('pseudo')) {

          $data_comment = [
              'news' => $request->getData('news'),
              'contenu' => $request->postData('contenu')
          ];
          $comment = new Comment($data_comment);

          if($request->postData('pseudoid') == 0):
              $comment->setPseudo($request->postData('pseudo'));
          else:
              $comment->setAuteur(new Member([
                  'id' => $request->postData('pseudoid')
              ]));
          endif;


          if (!$comment->isValid()):
              $response['success'] = false;
              $response['field'] = "commpentaire";
              $response['form'] = "Le commentaire n'est pas valide.";
          elseif($comment->pseudo() != null && $this->managers->getManagerOf('Member')->getMembercUsingNickname($comment->pseudo()) != null ):
              $response['success'] = false;
              $response['field'] = "pseudo";
              $response['form'] = "Ce pseudo est deja utilisé !";
          else:
              $this->managers->getManagerOf('Comments')->save($comment);
              if($comment['pseudo'] == null):
                  $pseudo = $comment->auteur()->nickname();
                  $user = ' - <a href='.Direction::askRoute('Backend','News','updateComment',array('id' => $comment['id'])).'>Modifier</a> | <a href='.Direction::askRoute('Backend','News','deleteComment',array('id' =>$comment['id'])).'>Supprimer</a>';
              else:
                  $pseudo = htmlspecialchars($comment['pseudo']);
                  $user = '';
              endif;

              $response['contenu'] = '<fieldset><legend>Posté par <strong>'.$pseudo.'</strong> le '.$comment['date']->format('d/m/Y à H\hi') .$user. '</legend><p>'.nl2br($comment['contenu']).'</p></fieldset>';
          endif;
          


      }else{
           $response['success'] = false;
          $response['errormessage'] = "Il n'y a pas de pseudo ou de contenu.";
      }

      $this->page->addVar('json',$response);

      return true;
  }
}