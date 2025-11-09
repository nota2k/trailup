<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReadableArrayType extends AbstractType implements DataTransformerInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer($this);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => ['readonly' => true],
            'expanded' => false,
                'multiple' => false,
                    'choices'  => [
                        'Facile' => true,
                        'Intermediaire' => true,
                        'Difficile' => true,
                    ],
                 // Pour empêcher la modification du champ
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $data = $form->getData();
        $view->vars['readable_value'] = implode(', ', $data);
    }

    public function transform($value)
    {
        return $value;
    }

    public function reverseTransform($value)
    {
        return unserialize($value);
    }

    public function getParent()
    {
        return TextType::class;
    }
}
