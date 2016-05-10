<?phpnamespace App\Backend\Modules\News;use \OCFram\BackController;use \OCFram\HTTPRequest;use \OCFram\Direction;use \FormBuilder\CommentFormBuilder;use \FormBuilder\CommentVisitorFormBuilder;use \FormBuilder\NewsFormBuilder;use \Entity\News;use \Entity\Comment;class NewsController extends BackController{    public function executeIndex(HTTPRequest $request)    {        $this->page->addVar('title', 'Gestion des news');        $manager = $this->managers->getManagerOf('News');        switch($this->app()->user()->getAttribute('user_status')){            case 1:                $this->page->addVar('listeNews', $manager->getList());                $this->page->addVar('nombreNews', $manager->count());                break;            case 2:                $listeNews = $manager->getListByAuteurId($this->app()->user()->getAttribute('user_id'));                $this->page->addVar('listeNews', $listeNews);                $this->page->addVar('nombreNews', count($listeNews));                break;            default:                break;        }        $this->page->addVar('user_id',$this->app()->user()->getAttribute('user_id'));        $this->page->addVar('user_status',$this->app()->user()->getAttribute('user_status'));        return true;    }    public function executeInsert(HTTPRequest $request)    {        $this->processForm($request);        $this->page->addVar('title', 'Ajout d\'une news');        return true;    }    public function processForm(HTTPRequest $request)    {        if ($request->method() == 'POST')        {            $news = new News([                'titre' => $request->postData('titre'),                'contenu' => $request->postData('contenu')            ]);            if ($request->getExists('id'))            {                $news->setId($request->getData('id'));            }        }        else        {            // L'identifiant de la news est transmis si on veut la modifier            if ($request->getExists('id'))            {                $news = $this->managers->getManagerOf('News')->getUnique($request->getData('id'));            }            else            {                $news = new News;            }        }        $news->setAuteurId($this->app()->user()->getAttribute('user_id'));        $news->setAuteur($this->app()->user()->getAttribute('user_name'));        $formBuilder = new NewsFormBuilder($news);        $formBuilder->build();        $form = $formBuilder->form();        if ($request->method() == 'POST' && $form->isValid())        {            $this->managers->getManagerOf('News')->save($news);            $this->app->user()->setFlash($news->isNew() ? 'La news a bien été ajoutée !' : 'La news a bien été modifiée !');            $this->app->httpResponse()->redirect('/admin/');        }        $this->page->addVar('form', $form->createView());        return true;    }    public function executeUpdate(HTTPRequest $request)    {        if($this->app()->user()->getAttribute('user_status') == 1 || $this->managers->getManagerOf('News')->getUnique($request->getData('id'))->auteurId() == $this->app()->user()->getAttribute('user_id') ) {            $this->processForm($request);            $this->page->addVar('title', 'Modification d\'une news');        }else{            $this->app->httpResponse()->redirect('.');        }        return true;    }    public function executeDelete(HTTPRequest $request)    {        if ($this->app()->user()->getAttribute('user_status') == 1 || $this->managers->getManagerOf('News')->getUnique($request->getData('id'))->auteurId() == $this->app()->user()->getAttribute('user_id')) {            $newsId = $request->getData('id');            $this->managers->getManagerOf('News')->delete($newsId);            $this->managers->getManagerOf('Comments')->deleteFromNews($newsId);            $this->app->user()->setFlash('La news a bien été supprimée !');        }        $this->app->httpResponse()->redirect('.');        return true;    }    public function executeUpdateComment(HTTPRequest $request)    {        if($this->app()->user()->getAttribute('user_status') == 1 || $this->managers->getManagerOf('Comments')->getUnique($request->getData('id'))->auteurId() == $this->app()->user()->getAttribute('user_id'))        {            $this->page->addVar('title', 'Modification d\'un commentaire');            if ($request->method() == 'POST')            {                $comment = new Comment([                    'id' => $request->getData('id'),                    'auteur' => $request->postData('auteur'),                    'contenu' => $request->postData('contenu')                ]);                $comment_old = $this->managers->getManagerOf('Comments')->getUnique($request->getData('id'));                $comment->setAuteurId($comment_old->auteurId());                $comment->setNews($comment_old->news());                if(!$comment->isValid())                    $comment->setAuteur($comment_old->auteur());            }            else            {                $comment = $this->managers->getManagerOf('Comments')->getUnique($request->getData('id'));            }            if($comment->auteurId() == 0){                $formBuilder = new CommentFormBuilder($comment);            }else{                $formBuilder = new CommentVisitorFormBuilder($comment);            }            $formBuilder->build();            $form = $formBuilder->form();            if ($request->method() == 'POST' && $form->isValid())            {                $this->managers->getManagerOf('Comments')->save($comment);                $this->app->user()->setFlash('Le commentaire a bien été modifié');                $this->app->httpResponse()->redirect(Direction::askRoute('Frontend','News','show',['id' => $comment->news()]));            }            $this->page->addVar('form', $form->createView());        }else{            $this->app->httpResponse()->redirect('.');        }        return true;    }    public function executeDeleteComment(HTTPRequest $request)    {        if($this->app()->user()->getAttribute('user_status') == 1 || $this->managers->getManagerOf('Comments')->getUnique($request->getData('id'))->auteurId() == $this->app()->user()->getAttribute('user_id')) {            $comment = $this->managers->getManagerOf('Comments')->getUnique($request->getData('id'));            $this->managers->getManagerOf('Comments')->delete($request->getData('id'));            $this->app->user()->setFlash('Le commentaire a bien été supprimé !');            $this->app->httpResponse()->redirect(Direction::askRoute('Frontend','News','show',['id' => $comment->news()]));        }        $this->app->httpResponse()->redirect('.');        return true;    }}