<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array(
                'attr' => array(
                    'placeholder' => 'Title',
                    'class' => 'recipe-title-input'
                )
            ))
            ->add('description', null, array(
                'attr' => array(
                    'placeholder' => 'Description',
                    'class' => 'recipe-description-input'
                )
            ))
            ->add('ingredients', CollectionType::class, array(
                'entry_type' => IngredientType::class,
                'allow_add' => true,
                'allow_delete' => true
            ))
            ->add('directions', CollectionType::class, array(
                'entry_type' => DirectionType::class,
                'allow_add' => true,
                'allow_delete' => true
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Recipe'
        ));
    }
}
