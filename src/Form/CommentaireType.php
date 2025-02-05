<?php

namespace App\Form;

use App\Entity\Commentaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required'=>false,
                'constraints' => [new Length(['min' => 2, 'max' => 30,'minMessage' => 'Le nom doit comporter au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.',]) ],
                'attr' => [
                    'placeholder' => 'Optionnel'
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required'=>false,
                'constraints' => [new Length(['min' => 2, 'max' => 30,'minMessage' => 'Le prénom doit comporter au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères.',]) ],
                'attr' => [
                    'placeholder' => 'Optionnel'
                ]
            ])
            ->add('email', EmailType::class, [
                'required'=>false,
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'Optionnel'
                ]
            ])
            ->add('message', TextareaType::class, [
                'required'=>false,
                'label' => 'Message',
                'constraints' => [new NotBlank(["message"=>'Le message ne peut pas être vide'])],
                'attr' => [
                    'placeholder' => 'Donnez vôtre avis'// avant: saisir votre message
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Envoyer"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaire::class,
        ]);
    }
}
