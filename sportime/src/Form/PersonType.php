<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use App\Entity\Person;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class PersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class)
            ->add('name', TextType::class)
			->add('last_name', TextType::class)
            ->add('birthday', DateType::class)
            ->add('weight', NumberType::class)
            ->add('height', NumberType::class)
            ->add('name', TextType::class)
            ->add('nationality', TextType::class)
			;
    }
	
	public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profesor::class,
			'csrf_protection'   => false,
        ]);
    }
	
	
}