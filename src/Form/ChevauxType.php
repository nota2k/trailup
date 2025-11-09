<?php

namespace App\Form;

use App\Entity\Chevaux;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChevauxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('race')
            ->add('age')
            ->add('photo01')
            ->add('photo02')
            ->add('photo03')
            ->add('sexe')
            ->add('proprietaire')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chevaux::class,
        ]);
    }
}
