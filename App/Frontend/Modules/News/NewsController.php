<?php
namespace App\Frontend\Modules\News;
 
use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \OCFram\Direction;
use \Entity\Comment;
use \Entity\News;
use \Entity\Member;
 
class NewsController extends BackController
{
    public function pop($tableau){
        if(!empty($tableau)){
            return array_pop($tableau);
        }
        return null;
    }
    
  public function executeIndex(HTTPRequest $request)
  {
    $nombreNews = $this->app->config()->get('nombre_news');
    $nombreCaracteres = $this->app->config()->get('nombre_caracteres');

    // On ajoute une définition pour le titre.
    $this->page->addVar('title', 'Liste des ' . $nombreNews . ' dernières news ');
    $this->app->user()->setAttribute('news_offset',$nombreNews);

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
                  'id' => $request->postData('pseudoid'),
                  'nickname' => $request->postData('pseudo')
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

              $user ='';
              if($comment['pseudo'] == null):
                  $pseudo = '<a href='.Direction::askRoute('Frontend','News','user',['id' => $comment->auteur()->id()]).'>'.$comment->auteur()->nickname().'</a>';
                  if($this->app()->user()->getAttribute('user_status') == 1 || $this->managers->getManagerOf('Comments')->getUnique($request->getData('id'))->auteur()->id() == $this->app()->user()->getAttribute('user_id') ):
                      $user = ' - <a href='.Direction::askRoute('Backend','News','updateComment',array('id' => $comment['id'])).'>Modifier</a> | <a class="link-delete" href='.Direction::askRoute('Backend','News','deleteComment',array('id' =>$comment['id'])).'>Supprimer</a>';
                  endif;
              else:
                  $pseudo = htmlspecialchars($comment['pseudo']);
                  if($this->app()->user()->getAttribute('user_status') == 1):
                      $user = ' - <a href='.Direction::askRoute('Backend','News','updateComment',array('id' => $comment['id'])).'>Modifier</a> | <a class="link-delete" href='.Direction::askRoute('Backend','News','deleteComment',array('id' =>$comment['id'])).'>Supprimer</a>';
                  endif;
              endif;

              $response['contenu'] = '<fieldset style="display:none" class="new comment"><legend>Posté par <strong>'.$pseudo.'</strong> le '.$comment['date']->format('d/m/Y à H\hi') .$user. '</legend><p>'.nl2br($comment['contenu']).'</p></fieldset>';

          endif;

      }else{
           $response['success'] = false;
          $response['errormessage'] = "Il n'y a pas de pseudo ou de contenu.";
      }

      $this->page->addVar('json',$response);
  }

  public function executeUser(HTTPRequest $request){
      $auteurid = $request->getData('id');

      $member = $this->managers->getManagerOf('Member')->getMembercUsingId($auteurid);

      $this->page->addVar('title',"Feed de ".$member->nickname());
      $this->page->addVar('nickname',$member->nickname());

      $listFeed = array_merge($this->managers->getManagerOf('Comments')->getListBy($auteurid), $this->managers->getManagerOf('News')->getListCreatBy($auteurid), $this->managers->getManagerOf('News')->getListModifBy($auteurid));

      usort($listFeed,function($a,$b){
          $datea = null;
          $dateb = null;
          if($a instanceof Comment):
              $datea = $a->date();
          elseif($a instanceof News && $a->dateModif() == null):
              $datea = $a->dateAjout();
          elseif($a instanceof News):
              $datea = $a->dateModif();
          endif;

          if($b instanceof Comment):
              $dateb = $b->date();
          elseif($b instanceof News && $b->dateModif() == null):
              $dateb = $b->dateAjout();
          elseif($a instanceof News):
              $dateb = $b->dateModif();
          endif;

          if($datea > $dateb){
              return -1;
          }
          return 1;
      });

      $this->page->addVar('listeFeed',$listFeed);
  }

  public function executeBefore(HTTPRequest $request){
      $commentid = $request->getData('id');
      $newsid = $request->getData('news');

      $comment = $this->managers->getManagerOf('Comments')->getCommentBefore($newsid,$commentid);
      $response = [];
      $response['success'] = true;
      if($comment !=  null) :
          $response['success'] = true;
          $response['link'] = Direction::askRoute('Frontend', 'News', 'before', ['news' => $newsid, 'id' => $comment->id()]);

          $user = '';
          if($comment->pseudo() == null):
              $pseudo = '<a href='.Direction::askRoute('Frontend','News','user',['id' => $comment->auteur()->id()]).'>'.$comment->auteur()->nickname().'</a>';
              if($this->app->user()->getAttribute('user_id') == $comment->auteur()->id() || $this->app()->user()->getAttribute('user_status') == 1 ):
                  $user = ' - <a href=' . Direction::askRoute('Backend', 'News', 'updateComment', array('id' => $comment->id())) . '>Modifier</a> | <a class="link-delete" href=' . Direction::askRoute('Backend', 'News', 'deleteComment', array('id' => $comment->id())) . '>Supprimer</a>';
              endif;
          else:
              $pseudo = htmlspecialchars($comment['pseudo']);
              if($this->app->user()->getAttribute('user_status') == 1):
                  $user = ' - <a href=' . Direction::askRoute('Backend', 'News', 'updateComment', array('id' => $comment->id())) . '>Modifier</a> | <a class="link-delete" href=' . Direction::askRoute('Backend', 'News', 'deleteComment', array('id' => $comment->id())) . '>Supprimer</a>';
              endif;
          endif;

          $comment2 = $this->managers->getManagerOf('Comments')->getCommentBefore($newsid,$comment->id());
          if($comment2 == null):
              $response['next'] = false;
          else:
              $response['next'] = true;
          endif;

          $response['contenu'] = '<fieldset style="display:none" class="comment"><legend>Posté par <strong>' . $pseudo . '</strong> le ' . $comment->date()->format('d/m/Y à H\hi') . $user . '</legend><p>' . nl2br($comment->contenu()) . '</p></fieldset>';
      else:
          $response['success'] = false;

      endif;

      $this->page->addVar('json',$response);
  }

  public function executeAfter(HTTPRequest $request){
        $commentid = $request->getData('id');
        $newsid = $request->getData('news');

        $comment = $this->managers->getManagerOf('Comments')->getCommentAfter($newsid,$commentid);
      $response = [];
      if($comment !=  null) :
          $response['success'] = true;
          $response['link'] = Direction::askRoute('Frontend', 'News', 'after', ['news' => $newsid, 'id' => $comment->id()]);

          $user = '';
          if($comment->pseudo() == null):
              $pseudo = '<a href='.Direction::askRoute('Frontend','News','user',['id' => $comment->auteur()->id()]).'>'.$comment->auteur()->nickname().'</a>';
              if($this->app->user()->getAttribute('user_id') == $comment->auteur()->id() || $this->app()->user()->getAttribute('user_status') == 1 ):
                  $user = ' - <a href=' . Direction::askRoute('Backend', 'News', 'updateComment', array('id' => $comment->id())) . '>Modifier</a> | <a class="link-delete" href=' . Direction::askRoute('Backend', 'News', 'deleteComment', array('id' => $comment->id())) . '>Supprimer</a>';
              endif;
          else:
              $pseudo = htmlspecialchars($comment['pseudo']);
              if($this->app->user()->getAttribute('user_status') == 1):
                  $user = ' - <a href=' . Direction::askRoute('Backend', 'News', 'updateComment', array('id' => $comment->id())) . '>Modifier</a> | <a class="link-delete" href=' . Direction::askRoute('Backend', 'News', 'deleteComment', array('id' => $comment->id())) . '>Supprimer</a>';
              endif;
          endif;

          $comment2 = $this->managers->getManagerOf('Comments')->getCommentAfter($newsid,$comment->id());
          if($comment2 == null):
              $response['next'] = false;
          else:
              $response['next'] = true;
          endif;

          $response['contenu'] = '<fieldset style="display:none" class="comment"><legend>Posté par <strong>' . $pseudo . '</strong> le ' . $comment->date()->format('d/m/Y à H\hi') . $user . '</legend><p>' . nl2br($comment->contenu()) . '</p></fieldset>';
      else:
          $response['success'] = false;

      endif;

        $this->page->addVar('json',$response);
    }

  public function executeTag(HTTPRequest $request){
      $hashtag = $request->getData('tag');
      $this->page->addVar('hashtag',"#".$hashtag);
      $this->page->addVar('title', 'Liste des news avec #'.$hashtag);
      
      $nombreCaracteres = $this->app->config()->get('nombre_caracteres');

      $listeNews = $this->managers->getManagerOf('News')->getListByTag($hashtag);

      foreach ($listeNews as $news):
          if (strlen($news->contenu()) > $nombreCaracteres):
              $debut = substr($news->contenu(), 0, $nombreCaracteres);
              $debut = substr($debut, 0, strrpos($debut, ' ')) . '...';

              $news->setContenu($debut);
          endif;
      endforeach;

      $this->page->addVar('listeNews', $listeNews);

  }

  public function executeStarting(HTTPRequest $request){
      $limit = $request->getData('lim');

      if($request->getExists('tag')) {
          $tag = $request->getData('tag');
          $listeTag = $this->managers->getManagerOf('News')->getTagcUsingStartDescription($tag, $limit);
      }else{
          $listeTag = $this->managers->getManagerOf('News')->getTagcUsingStartDescription('', $limit);
      }

          $response = [];
          if (empty($listeTag)):
              $response['success'] = false;
              $response['error'] = "Pas de tag";
          else:
              $response['success'] = true;
              $response['contenu'] = $listeTag;

          endif;


      $this->page->addVar('json',$response);
  }

  public function executeExist(HTTPRequest $request){
      $attribute = $request->getData('attribute');
      $val = $request->postData('value');
      $method = 'getMembercUsing'.ucfirst($attribute);
      $member = $this->managers->getManagerOf('Member')->$method($val);

      $response = [];
      if($member == null):
          $response['success'] = false;
      else:
          $response['success'] = true;
      endif;

      $this->page->addVar('json',$response);
  }

  public function executeScroll(HTTPRequest $request){
      $offset = $request->app()->user()->getAttribute('news_offset');
      $limit = $this->app->config()->get('nombre_news');
      $nombreCaracteres = $this->app->config()->get('nombre_caracteres');

      $manager = $this->managers->getManagerOf('News');

      $listeNews = $manager->getList($offset, $limit);

      $this->app->user()->setAttribute('news_offset',$offset + $limit);

      $respons = [];
      if(count($listeNews) < $limit):
          $response['next'] = false;
      else:
          $response['next'] = true;
      endif;

      foreach ($listeNews as $news) {
          if (strlen($news->contenu()) > $nombreCaracteres) {
              $debut = substr($news->contenu(), 0, $nombreCaracteres);
              $debut = substr($debut, 0, strrpos($debut, ' ')) . '...';

              $news->setContenu($debut);
          }
      }
      $response['contenu'] = $listeNews;

      $this->page->addVar('json',$response);
  }

}