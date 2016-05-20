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
        if (!$request->postExists('login')):
            $this->page->addVar('title', 'Inscription');

            $Member = new Member();


            $formBuilder = new AuthorFormBuilder($Member);
            $formBuilder->build();
            $form = $formBuilder->form();

            $this->page->addVar('form', $form->createView());

        else:

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
            if (!$form->isValid()):
                $response['success'] = false;
                $response['form'] = $form->error();
            else:
                $response['success'] = true;
                $Member->setStatus(2);
                $Member->setHash(crypt(CRYPT_BLOWFISH, $Member->password1()));
                $this->managers->getManagerOf('Member')->save($Member);
                $this->app->user()->setFlash('Votre compte a bien été crée !');

                $this->app->user()->setAuthenticated(true);
                $this->app->user()->setAttribute('user_id', $Member->id());
                $this->app->user()->setAttribute('user_status', $Member->status());
                $this->app->user()->setAttribute('user_name', $Member->nickname());


            endif;

            $this->page->addVar('json', $response);
        endif;
    }

    public function executeinsertAuthor(HTTPRequest $request)
    {
        if ($request->postExists('login')) {
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
            if (!$form->isValid()) {
                $response['success'] = false;
                $response['form'] = $form->error();
            } else {
                $response['success'] = true;
                $Member->setStatus(2);
                $Member->setHash(crypt(CRYPT_BLOWFISH, $Member->password1()));
                $this->managers->getManagerOf('Member')->save($Member);
                $this->app->user()->setFlash('Votre compte a bien été crée !');

                $this->app->user()->setAuthenticated(true);
                $this->app->user()->setAttribute('user_id', $Member->id());
                $this->app->user()->setAttribute('user_status', $Member->status());
                $this->app->user()->setAttribute('user_name', $Member->nickname());
            }

            $this->page->addVar('json', $response);
        }

        return true;

    }
}