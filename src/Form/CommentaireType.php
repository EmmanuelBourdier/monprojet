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
                'constraints' => [new Length(['min' => 2, 'max' => 30]) ],
                'attr' => [
                    'placeholder' => 'Saisir votre Nom (optionnel)'
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required'=>false,
                'constraints' => [new Length(['min' => 2, 'max' => 30]) ],
                'attr' => [
                    'placeholder' => 'Saisir votre Prénom (optionnel)'
                ]
            ])
            ->add('email', EmailType::class, [
                'required'=>false,
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'Saisir votre Email (optionnel)'
                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'constraints' => [new NotBlank()],
                'attr' => [
                    'placeholder' => 'saisir votre message'
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
