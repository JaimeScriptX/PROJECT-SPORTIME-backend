<?php

namespace App\Form\Type;

use App\Entity\Events;
use App\Form\Model\EventsDto;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class EventsFormType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder, 
        array $options
    ){
       $builder
           # ->add('fk_sport_id', IntegerType::class)
           # ->add('fk_sportcenter_id', IntegerType::class)
           # ->add('fk_difficulty_id', IntegerType::class)
           # ->add('fk_sex_id', IntegerType::class)
           # ->add('fk_person_id', IntegerType::class)
            ->add('fk_team_colours', CollectionType::class, [
                'entry_type' => TeamColorFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,

            ])
            ->add('name')
            ->add('is_private', CheckboxType::class, [
                'required' => false,
            ])
            ->add('details', TextareaType::class, [
                'required' => false,
            ])
            ->add('price', IntegerType::class, [
                'required' => false,
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('time', TimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('duration', TimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('number_players', IntegerType::class)
            ->add('submit', SubmitType::class)
        ;
            
                
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EventsDto::class,
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