<?php
namespace App\Backend\Modules\Connexion;

use Entity\Member;
use \OCFram\BackController;
use \OCFram\HTTPRequest;

class ConnexionController extends BackController
{
    public function executeIndex(HTTPRequest $request)
    {
        $this->page->addVar('title', 'Connexion');

        if ($request->postExists('login')) {
            $login = $request->postData('login');
            $manager = $this->managers->getManagerOf('Member');
            $password = $request->postData('password');

            $member = $manager->getMembercUsingLogin($login);

            if ($member->hash() == crypt(CRYPT_BLOWFISH,$password))
            {
                $this->app->user()->setAuthenticated(true);
                $this->app->user()->setAttribute('user_id',$member['id']);
                $this->app->user()->setAttribute('user_status',$member['status']);
                $this->app->user()->setAttribute('user_name',$member['nickname']);
                $this->app->httpResponse()->redirect('.');
            }
            else
            {
                $this->app->user()->setFlash('Le pseudo ou le mot de passe est incorrect.');
            }
            
        }
        return true;
    }

    public function executeLogout(HTTPRequest $request)
    {
        if ($this->app->user()->isAuthenticated()){
            
            $this->app->user()->setAuthenticated(false);

            session_unset();
            session_destroy();
            session_start();

            $this->app->httpResponse()->redirect('/home ');

        }
        return true;
    }
}