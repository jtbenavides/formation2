<?php
namespace App\Frontend\Modules\News;
 
use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \OCFram\Direction;
use \Entity\Comment;
 
class NewsController extends BackController
{
    private static $state_form = 1;
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
              'auteur' => $request->postData('pseudo'),
              'contenu' => $request->postData('contenu'),
              'auteurId' => $request->postData('pseudoid')
          ];

          $comment = new Comment($data_comment);

          if (!$comment->isValid()) {
              $response['success'] = false;
              $response['errormessage'] = "Le commentaire n'est pas valide.";
              echo json_encode($response);
              return false;
          }else if(!$this->managers->getManagerOf('Comments')->unique($comment)) {
              $response['success'] = false;
              $response['errormessage'] = "Ce commentaire existe deja.";
              echo json_encode($response);
              return false;
          }else{
                  $this->managers->getManagerOf('Comments')->save($comment);
          }
          
          if ($this->app()->user()->isAuthenticated()) {
              $user = ' - <a href='.Direction::askRoute('Backend','News','updateComment',array('id' => $comment['id'])).'>Modifier</a> | <a href='.Direction::askRoute('Backend','News','deleteComment',array('id' =>$comment['id'])).'>Supprimer</a>';
          }else{
              $user = '';
          }
          
          $response['contenu'] = '<fieldset><legend>Posté par <strong>'.htmlspecialchars($comment['auteur']).'</strong> le '.$comment['date']->format('d/m/Y à H\hi') .$user. '</legend><p>'.nl2br($comment['contenu']).'</p></fieldset>';
      }else{
           $response['success'] = false;
          $response['errormessage'] = "Il n'y a pas de pseudo ou de contenu.";
      }

      echo json_encode($response);

      return false;
  }
}