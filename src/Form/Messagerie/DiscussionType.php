<?php

namespace App\Form\Messagerie;

use App\Entity\Messagerie\Discussions;
use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiscussionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentUser = $options['current_user'];
        
        $builder
            ->add('user2', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'username',
                'label' => 'Destinataire',
                'query_builder' => function (UtilisateurRepository $er) use ($currentUser) {
                    return $er->createQueryBuilder('u')
                        ->where('u.id != :currentUserId')
                        ->setParameter('currentUserId', $currentUser->getId())
                        ->orderBy('u.username', 'ASC');
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('sujet', TextType::class, [
                'label' => 'Sujet',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Sujet de la conversation'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Discussions::class,
            'current_user' => null,
        ]);
    }
}

