<?php

namespace App\Form;

use App\Entity\Itineraires;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Form\TextareaType;
use App\Form\ReadableArrayType;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

class ItinerairesType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', null, [
                'label' => false
            ])
            ->add('publie', ChoiceType::class, [
                    'label' => false,
                    'choices'  => [
                        'Oui' => true,
                        'Non' => false,
                    ],
                ])
            ->add('niveau', ChoiceType::class, [
                'label' => false,
                'expanded' => false,
                'multiple' => true,
                    'choices'  => [
                        'Facile' => 'Facile',
                        'Intermediaire' => 'Intermediaire',
                        'Difficile' => 'Difficile',
                    ],  
                ])
            ->add('allures', ChoiceType::class, [
                'label' => false,
                'expanded' => true,
                'multiple' => true,
                    'choices'  => [
                        'Pas' => 'Pas',
                        'Trot' => 'Trot',
                        'Galop' => 'Galop',
                    ],
                ])
            ->add('depart', null, array('label' => false))
            ->add('codePostal', null, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'code-postal-input',
                    'placeholder' => 'Code postal'
                ]
            ])
            ->add('distance')
            ->add('duree')
            ->add('accepte', ChoiceType::class, [
                'expanded' => true,
                'multiple' => true,
                'label' => false,
                'choices'  => [
                    'Jument' => 'Jument',
                    'Hongre' => 'Hongre',
                    'Entier' => 'Entier',
                ]
            ])
            ->add('description', null, array('label' => false));
            // ->add('utilisateur')
        ;   

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Itineraires::class,
        ]);
    }
}
