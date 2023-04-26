<?php

namespace App\Form\Type;

use App\Entity\Events;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\DBAL\Types\TimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class EventsFormType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder, 
        array $options
    ){
        $builder
            ->add('image_profile', TextType::class)
            ->add('name', TextType::class)
            ->add('is_private', BooleanType::class)
            ->add('details', TextType::class)
            ->add('price', NumberType::class)
            ->add('date', DateType::class)
            ->add('time', TimeType::class)
            ->add('duration', TimeType::class)
            ->add('number_players', NumberType::class);
            #faltan: 
                #fk_sport
                #fk_sportcenter
                #fk_difficulty
                #fk_sex
                #fk_person
                #eventPlayers
                #fk_team_colours
                # events
            
                
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Events::class,
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