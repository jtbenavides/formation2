<?php
namespace FormBuilder;

use OCFram\ComparisonValidator;
use OCFram\EmailDatabaseValidator;
use OCFram\EmailPatternValidator;
use \OCFram\FormBuilder;
use OCFram\LoginDatabaseValidator;
use OCFram\NicknameDatabaseValidator;
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
                new MaxLengthValidator('Le login spécifié est trop long (24 caractères maximum)', 24),
                new NotNullValidator('Merci de spécifier le login'),
                new LoginDatabaseValidator('Ce login est deja utilisé')
            ],
        ]))
            ->add(new StringField([
                'label' => 'Pseudo',
                'name' => 'nickname',
                'maxLength' => 24,
                'validators' => [
                    new MaxLengthValidator('Le pseudo spécifié est trop long (24 caractères maximum)', 24),
                    new NotNullValidator('Merci de spécifier le pseudo'),
                    new NicknameDatabaseValidator('Ce pseudo est deja utilisé')
                ],
            ]))
            ->add(new StringField([
                'label' => 'Email',
                'name' => 'email',
                'validators' => [
                    new NotNullValidator('Merci de spécifier un email'),
                    new EmailPatternValidator('Merci de spécifier un email valide.'),
                    new EmailDatabaseValidator('Cet email est deja utilisé')
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