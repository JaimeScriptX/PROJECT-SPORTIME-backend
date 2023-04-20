<?php

namespace App\Form\Type;

use App\Entity\Person;
use Doctrine\DBAL\Types\FloatType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonFormType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder, 
        array $options
    ){
        $builder
            ->add('name', TextType::class)
            ->add('image_profile', TextType::class)
            ->add('last_name', TextType::class)
            ->add('birthday', DateType::class)
            ->add('weight', FloatType::class)
            ->add('height', FloatType::class)
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