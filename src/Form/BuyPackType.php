<?php
// src/Form/BuyPackType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BuyPackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pack', ChoiceType::class, [
                'choices' => [
                    'Pack 1' => 1,
                    'Pack 2' => 2,
                    'Pack 3' => 3,
                ],
                'label' => 'Sélectionnez un pack',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}