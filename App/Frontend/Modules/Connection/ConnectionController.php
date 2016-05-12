<?php
namespace App\Frontend\Modules\Connection;

use Entity\Member;
use FormBuilder\AuthorFormBuilder;
use \OCFram\BackController;
use \OCFram\HTTPRequest;

class ConnectionController extends BackController
{
    public function executeSignin(HTTPRequest $request)
    {
        $this->page->addVar('title', 'Inscription');

        $Member = new Member();


        $formBuilder = new AuthorFormBuilder($Member);
        $formBuilder->build();
        $form = $formBuilder->form();

        $this->page->addVar('form',$form->createView());

        return true;
    }

    public function executeinsertAuthor(HTTPRequest $request){
        if ($request->postExists('login'))
        {
            $Member = new Member([
                'login' => $request->postData('login'),
                'nickname' => $request->postData('nickname'),
                'email' => $request->postData('email'),
                'password1' => $request->postData('password1'),
                'password2' => $request->postData('password2'),
            ]);

            $formBuilder = new AuthorFormBuilder($Member);
            $formBuilder->build();
            $form = $formBuilder->form();
            $response = [];
            if(!$form->isValid()) {
                $response['success'] = false;
                $response['form'] = $form->error();

            }elseif($this->managers->getManagerOf('Member')->getMembercUsingLogin($Member->login()) != null ){
                $response['success'] = false;
                $response['field'] = "login";
                $response['form'] = "Ce login est deja utilisé !";
            }elseif($this->managers->getManagerOf('Member')->getMembercUsingNickname($Member->nickname()) != null ){
            $response['success'] = false;
            $response['field'] = "pseudo";
            $response['form'] = "Ce pseudo est deja utilisé !";
            }else{
                $response['success'] = true;
                $Member->setStatus(2);
                $Member->setHash(crypt(CRYPT_BLOWFISH,$Member->password1()));
                $this->managers->getManagerOf('Member')->save($Member);
                $this->app->user()->setFlash('Votre compte a bien été crée !');

                $this->app->user()->setAuthenticated(true);
                $this->app->user()->setAttribute('user_id',$Member->id());
                $this->app->user()->setAttribute('user_status',$Member->status());
                $this->app->user()->setAttribute('user_name',$Member->nickname());
            }

            $this->page->addVar('json',$response);
        }

        return true;

    }
}