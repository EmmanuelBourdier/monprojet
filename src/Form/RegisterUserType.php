<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RegisterUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('lastname', TextType::class, [
            'label' => 'Nom', 'attr' => ['placeholder' => 'Entrez votre nom']
        ])
        ->add('firstname', TextType::class, [
            'label' => 'Prénom', 'attr' => ['placeholder' => 'Entrez votre prénom']
        ])
        ->add('email', EmailType::class, [
            'label' => 'Adresse email', 'attr' => ['placeholder' => 'Entrez votre adresse email']
        ])
        ->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'first_options'  => [
                'label' => 'Mot de passe',
                'attr' => ['placeholder' => 'Entrez votre mot de passe',"id"=>"password"],
                'hash_property_path' => 'password'
            ],
            'second_options' => [
                'label' => 'Confirmer le mot de passe',
                'attr' => ['placeholder' => 'Confirmez votre mot de passe',"id"=>"password2"],
            ],
            'invalid_message' => 'Les mots de passe ne correspondent pas',
            'mapped' => false,
        ])

        
        ->add('submit', SubmitType::class, [
            'label' => 'Inscription', 'attr' => ['class' => 'btn btn-success']
        ])  

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints'=> [
                new UniqueEntity([
                    'entityClass' => User::class,
                    'fields' => ['email'],
                ])
                ],
            'data_class' => User::class,
        ]);
    }
}
