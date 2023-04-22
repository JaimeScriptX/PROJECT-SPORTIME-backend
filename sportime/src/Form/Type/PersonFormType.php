<?php

namespace App\Form\Type;

use App\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class PersonFormType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder, 
        array $options
    ){
        $builder
            ->add('image_profile', TextType::class)
            ->add('name', TextType::class)
            ->add('last_name', TextType::class)
            ->add('birthday', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ])
            ->add('weight', NumberType::class)
            ->add('height', NumberType::class)
            ->add('nationality', TextType::class);
            # Falta events, fk_sex y fk_eventPlayers  
            
                
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

    public function getName()
    {
        return '';
    }
}