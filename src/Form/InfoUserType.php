<?php

namespace App\Form;

use App\Entity\InfoUser;
use App\Entity\Utilisateur;

use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityManagerInterface;

use App\Form\DataTransformer\UserDataTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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
            ->add('miniature', null, [
                'label' => false])
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
