<?php
namespace FormBuilder;

use OCFram\ComparisonValidator;
use OCFram\EmailValidator;
use \OCFram\FormBuilder;
use OCFram\PasswordField;
use \OCFram\StringField;
use \OCFram\MaxLengthValidator;
use \OCFram\NotNullValidator;

class AuthorFormBuilder extends FormBuilder
{
    public function build()
    {
        $Confirm = new PasswordField([
            'label' => 'Confirmation de Mot de Passe',
            'name' => 'password2',
            'maxLength' => 24,
            'validators' => [
                new MaxLengthValidator('L\'auteur spécifié est trop long (50 caractères maximum)', 24),
                new NotNullValidator('Merci de spécifier un mot de passe'),
            ]
        ]);

        $Comparison = new ComparisonValidator('Les mots de passe ne sont pas les mêmes !');
        $Comparison->setField($Confirm);

        $this->form->add(new StringField([
            'label' => 'Login',
            'name' => 'login',
            'maxLength' => 24,
            'validators' => [
                new MaxLengthValidator('L\'auteur spécifié est trop long (24 caractères maximum)', 24),
                new NotNullValidator('Merci de spécifier le login'),
            ],
        ]))
            ->add(new StringField([
                'label' => 'Pseudo',
                'name' => 'nickname',
                'maxLength' => 24,
                'validators' => [
                    new MaxLengthValidator('L\'auteur spécifié est trop long (24 caractères maximum)', 24),
                    new NotNullValidator('Merci de spécifier le pseudo'),
                ],
            ]))
            ->add(new StringField([
                'label' => 'Email',
                'name' => 'email',
                'validators' => [
                    new NotNullValidator('Merci de spécifier l\'auteur du commentaire'),
                    new EmailValidator('Merci de spécifier un email valide.')
                ],
            ]))

            ->add(new PasswordField([
                'label' => 'Mot de Passe',
                'name' => 'password1',
                'maxLength' => 24,
                'validators' => [
                    new MaxLengthValidator('L\'auteur spécifié est trop long (50 caractères maximum)', 24),
                    new NotNullValidator('Merci de spécifier un mot de passe'),
                    $Comparison
                ],
            ]))

            ->add($Confirm);
    }
}