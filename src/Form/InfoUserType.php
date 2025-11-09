<?php

namespace App\Form;

use App\Entity\InfoUser;
use App\Entity\Utilisateur;

use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityManagerInterface;

use App\Form\DataTransformer\UserDataTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\File;

use App\Form\DataTransformer\IssueToNumberTransformer;

class InfoUserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'label' => false])
            ->add('prenom', null, [
                'label' => false])
            ->add('ville', null, [
                'label' => false])
            ->add('region', null, [
                'label' => false])
            ->add('email', EmailType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
            ])
            ->add('miniatureFile', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, GIF ou WebP)',
                    ])
                ],
            ])
        ;

        // $builder->get('user')
        //         ->addModelTransformer($this->dataTransformer);
            
        // ;
        // $builder->addEventListener(FormEvent::PRE_SET_DATA, function (FormEvent $event): void {
        // });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InfoUser::class,
        ]);
    }
}
